<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class DataGridActionColumn
{
    public function __construct(
        private string $route,
        private array $routeAttributes = [],
        private ?array $routeAttributesCallback = null,
        private ?string $routeLocale = null,
        private int $order = 1,
        private ?string $label = null,
        private string $class = 'btn btn-primary btn-sm float-end',
        private ?string $iconClass = null,
    ) {
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getRouteLocale(): ?string
    {
        return $this->routeLocale;
    }

    public function getRouteAttributes(): array
    {
        return $this->routeAttributes;
    }

    public function getRouteAttributesCallback(): ?array
    {
        return $this->routeAttributesCallback;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getIconClass(): ?string
    {
        return $this->iconClass;
    }
}
