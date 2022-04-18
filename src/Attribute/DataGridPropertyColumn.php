<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DataGridPropertyColumn
{
    /** @var callable|null  */
    private mixed $routeAttributesCallback;

    /** @var callable|null  */
    private mixed $columnAttributesCallback;

    /** @var callable|null  */
    private mixed $valueCallback;

    public function __construct(
        private bool $sortable = false,
        private bool $filterable = false,
        private int $order = 0,
        private ?string $label = null,
        private ?string $class = null,
        ?callable $valueCallback = null,
        private bool $html = false,
        private ?string $route = null,
        private array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        private ?string $routeLocale = null,
        private ?string $routeRole = null,
        private array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
    ) {
        $this->valueCallback = $valueCallback;
        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
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

    public function getValueCallback(): ?callable
    {
        return $this->valueCallback;
    }

    public function isHtml(): bool
    {
        return $this->html;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getRouteAttributes(): array
    {
        return $this->routeAttributes;
    }

    public function getRouteAttributesCallback(): ?callable
    {
        return $this->routeAttributesCallback;
    }

    public function getRouteLocale(): ?string
    {
        return $this->routeLocale;
    }

    public function getRouteRole(): ?string
    {
        return $this->routeRole;
    }

    public function getColumnAttributes(): array
    {
        return $this->columnAttributes;
    }

    public function getColumnAttributesCallback(): ?callable
    {
        return $this->columnAttributesCallback;
    }
}
