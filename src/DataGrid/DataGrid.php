<?php

namespace Pageon\DoctrineDataGridBundle\DataGrid;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Pageon\DoctrineDataGridBundle\Column\Column;

final class DataGrid
{
    /**
     * @param Column[] $columns
     */
    public function __construct(
        private PaginationInterface $paginator,
        private array $columns,
        private string $noResultsMessage
    ) {
        usort($this->columns, static fn (Column $a, Column $b) => $a->getOrder() <=> $b->getOrder());
    }

    public function getPaginator(): PaginationInterface
    {
        return $this->paginator;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getNoResultsMessage(): string
    {
        return $this->noResultsMessage;
    }
}
