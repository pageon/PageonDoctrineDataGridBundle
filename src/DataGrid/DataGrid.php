<?php

namespace Pageon\DoctrineDataGridBundle\DataGrid;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Pageon\DoctrineDataGridBundle\Column\Column;
use Stringable;

final class DataGrid
{
    public readonly array $columns;
    public readonly string $noResultsMessage;

    /** @var callable|null */
    private mixed $rowAttributesCallback;

    /** @var array{string?:int|float|string|Stringable} */
    private array $rowAttributes;

    public function __construct(
        public readonly PaginationInterface $paginator,
        /** @var Column[] $columns */
        array $columns,
        string $noResultsMessage,
        /** @var array{string?:int|float|string|Stringable} $rowAttributes */
        array $rowAttributes = [],
        ?callable $rowAttributesCallback = null,
    ) {
        $this->rowAttributesCallback = $rowAttributesCallback;
        $this->rowAttributes = $rowAttributes;
        usort($columns, fn(Column $a, Column $b) => $a->order <=> $b->order);
        $this->columns = $columns;
        $this->noResultsMessage = $noResultsMessage;
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