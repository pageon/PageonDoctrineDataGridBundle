<?php

namespace Pageon\DoctrineDataGridBundle\DataGrid;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Pageon\DoctrineDataGridBundle\Column\Column;
use Stringable;

final class DataGrid
{
    /** @var callable|null */
    private mixed $rowAttributesCallback;

    public function __construct(
        private PaginationInterface $paginator,
        /** @var Column[] $columns */
        private array $columns,
        private string $noResultsMessage,
        /** @var array{string?:int|float|string|Stringable} $rowAttributes */
        private array $rowAttributes = [],
        ?callable $rowAttributesCallback = null,
    ) {
        $this->rowAttributesCallback = $rowAttributesCallback;
        usort($this->columns, static fn (Column $a, Column $b) => $a->getOrder() <=> $b->getOrder());
    }

    public function getPaginator(): PaginationInterface
    {
        return $this->paginator;
    }

    /** @return Column[] */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getNoResultsMessage(): string
    {
        return $this->noResultsMessage;
    }

    /** @return array{string?:int|float|string|Stringable} */
    public function getRowAttributes(object $entity): array
    {
        if ($this->rowAttributesCallback !== null) {
            return call_user_func($this->rowAttributesCallback, $entity, $this->rowAttributes);
        }

        return $this->rowAttributes;
    }
}
