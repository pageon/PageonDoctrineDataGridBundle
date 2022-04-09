<?php

namespace Pageon\DoctrineDataGridBundle\Column;

final class Column
{
    public function __construct(
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
        private ?string $iconClass = null,
        private ?array $valueCallback = null,
        private bool $html = false,
        private bool $showColumnLabel = true,
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
        ?string $route = null,
        array $routeAttributes = [],
        ?array $routeAttributesCallback = null,
        ?string $routeLocale = null,
        ?string $class = null,
        ?array $valueCallback = null,
        bool $html = false,
    ): self {
        return new self(
            name: $name,
            label: $label,
            entityAlias: $entityAlias,
            sortable: $sortable,
            filterable: $filterable,
            order: $order,
            route: $route,
            routeAttributes: $routeAttributes,
            routeAttributesCallback: $routeAttributesCallback,
            routeLocale: $routeLocale,
            class: $class,
            valueCallback: $valueCallback,
            html: $html,
        );
    }

    public static function createMethodColumn(
        string $name,
        string $label,
        int $order,
        ?string $route = null,
        array $routeAttributes = [],
        ?array $routeAttributesCallback = null,
        ?string $routeLocale = null,
        ?string $class = null,
        bool $html = false,
    ): self {
        return new self(
            name: $name,
            label: $label,
            order: $order,
            route: $route,
            routeAttributes: $routeAttributes,
            routeAttributesCallback: $routeAttributesCallback,
            routeLocale: $routeLocale,
            class: $class,
            html: $html,
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
        ?string $iconClass = null,
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
            iconClass: $iconClass,
            showColumnLabel: false,
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

    public function getIconClass(): ?string
    {
        return $this->iconClass;
    }

    public function getValue(mixed $value, mixed $row, string $columnName): mixed
    {
        if ($this->valueCallback !== null) {
            return call_user_func($this->valueCallback, $value, $row, $columnName);
        }

        return $value;
    }

    public function isHtml(): bool
    {
        return $this->html;
    }

    public function hasValueCallback(): bool
    {
        return $this->valueCallback !== null;
    }

    public function showColumnLabel(): bool
    {
        return $this->showColumnLabel;
    }
}
