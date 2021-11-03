<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DataGridPropertyColumn
{
    public function __construct(
        private bool $sortable = false,
        private bool $filterable = false,
        private int $order = 0,
        private ?string $label = null,
        private ?string $class = null,
        private ?array $valueCallback = null,
    ) {
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getValueCallback(): ?array
    {
        return $this->valueCallback;
    }
}
