<?php

namespace Pageon\DoctrineDataGridBundle\Column;

final class Column
{
    private function __construct(
        private string $name,
        private string $label,
        private ?string $entityAlias = null,
        private bool $sortable = false,
        private bool $filterable = false,
        private int $order = 0,
        private ?string $route = null,
        private array $routeAttributes = [],
        private ?array $routeAttributesCallback = null,
        ?string $routeLocale = null,
        private ?string $class = null,
    ) {
        if ($routeLocale !== null) {
            $this->routeAttributes['_locale'] = $routeLocale;
        }
    }

    public static function createPropertyColumn(
        string $name,
        string $label,
        ?string $entityAlias,
        bool $sortable,
        bool $filterable,
        int $order,
        ?string $class = null,
    ): self {
        return new self(
            name: $name,
            label: $label,
            entityAlias: $entityAlias,
            sortable: $sortable,
            filterable: $filterable,
            order: $order,
            class: $class,
        );
    }

    public static function createMethodColumn(
        string $label,
        int $order,
        ?string $class = null,
    ): self {
        return new self(
            name: $label,
            label: $label,
            order: $order,
            class: $class,
        );
    }

    public static function createActionColumn(
        string $label,
        int $order = 0,
        ?string $route = null,
        array $routeAttributes = [],
        ?array $routeAttributesCallback = null,
        ?string $routeLocale = null,
        ?string $class = null,
    ): self {
        return new self(
            name: $label,
            label: $label,
            order: $order,
            route: $route,
            routeAttributes: $routeAttributes,
            routeAttributesCallback: $routeAttributesCallback,
            routeLocale: $routeLocale,
            class: $class,
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    public function getFullName(): string
    {
        if ($this->entityAlias === null) {
            return $this->name;
        }

        return $this->entityAlias . '.' . $this->name;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getRouteAttributes(object $entity): array
    {
        if ($this->routeAttributesCallback !== null) {
            return $this->routeAttributes + call_user_func($this->routeAttributesCallback, $entity);
        }

        return $this->routeAttributes;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }
}
