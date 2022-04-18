<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class DataGridMethodColumn
{
    /** @var callable|null  */
    private mixed $routeAttributesCallback;

    /** @var callable|null  */
    private mixed $columnAttributesCallback;

    public function __construct(
        private int $order = 0,
        private ?string $label = null,
        private ?string $class = null,
        private bool $html = false,
        private ?string $route = null,
        private array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        private ?string $routeLocale = null,
        private ?string $routeRole = null,
        private array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
    ) {
        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
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
