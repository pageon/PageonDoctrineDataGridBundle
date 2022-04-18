<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class DataGridActionColumn
{
    /** @var callable|null  */
    private mixed $routeAttributesCallback;

    /** @var callable|null  */
    private mixed $columnAttributesCallback;

    /** @var callable|null  */
    private mixed $valueCallback;

    public function __construct(
        private string $route,
        private array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        private ?string $routeLocale = null,
        private int $order = 1,
        private ?string $label = null,
        private string $class = 'btn btn-primary btn-sm float-end',
        private ?string $iconClass = null,
        ?callable $valueCallback = null,
        private ?string $requiredRole = null,
        private array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
    ) {
        $this->valueCallback = $valueCallback;
        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
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

    public function getRouteAttributesCallback(): ?callable
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

    public function getValueCallback(): ?callable
    {
        return $this->valueCallback;
    }

    public function getRequiredRole(): ?string
    {
        return $this->requiredRole;
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
