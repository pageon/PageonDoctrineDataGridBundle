<?php

namespace Pageon\DoctrineDataGridBundle\Filtration;

use Doctrine\ORM\Query;
use Knp\Component\Pager\ArgumentAccess\ArgumentAccessInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Knp\Component\Pager\Event\Subscriber\Filtration\Doctrine\ORM\Query\WhereWalker;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\Query\Helper as QueryHelper;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UnexpectedValueException;

final readonly class QuerySubscriber implements EventSubscriberInterface
{
    public function __construct(private ArgumentAccessInterface $argumentAccess)
    {
    }

    public function items(ItemsEvent $event): void
    {
        if (!$event->target instanceof Query) {
            return;
        }

        $filterValue = $this->argumentAccess->get($event->options[PaginatorInterface::FILTER_VALUE_PARAMETER_NAME]);
        if ((empty($filterValue) && $filterValue !== '0')) {
            return;
        }

        $filterName = $this->argumentAccess->get($event->options[PaginatorInterface::FILTER_FIELD_PARAMETER_NAME]);
        if (!empty($filterName)) {
            $columns = $filterName;
        } elseif (!empty($event->options[PaginatorInterface::DEFAULT_FILTER_FIELDS])) {
            $columns = $event->options[PaginatorInterface::DEFAULT_FILTER_FIELDS];
        } else {
            return;
        }

        $value = $this->argumentAccess->get($event->options[PaginatorInterface::FILTER_VALUE_PARAMETER_NAME]);
        if (str_contains($value, '*')) {
            $value = str_replace('*', '%', $value);
        } else {
            $value = '%' . $value . '%'; // add default wildcard
        }

        if (is_string($columns) && str_contains($columns, ',')) {
            $columns = explode(',', $columns);
        }

        $columns = (array) $columns;

        if (isset($event->options[PaginatorInterface::FILTER_FIELD_ALLOW_LIST])) {
            $invalidColumn = array_find(
                $columns,
                fn(string $column) => !in_array(
                    $column,
                    $event->options[PaginatorInterface::FILTER_FIELD_ALLOW_LIST],
                    true
                )
            );
            if ($invalidColumn !== null) {
                throw new UnexpectedValueException(
                    "Cannot filter by: [{$invalidColumn}] this field is not in allow list"
                );
            }
        }

        $event->target
            ->setHint(WhereWalker::HINT_PAGINATOR_FILTER_VALUE, $value)
            ->setHint(WhereWalker::HINT_PAGINATOR_FILTER_COLUMNS, $columns);
        QueryHelper::addCustomTreeWalker($event->target, WhereWalker::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'knp_pager.items' => ['items', 0],
        ];
    }
}
