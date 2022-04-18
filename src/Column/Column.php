<?php

namespace Pageon\DoctrineDataGridBundle\Column;

final class Column
{
    /** @var callable  */
    private mixed $routeAttributesCallback;

    /** @var callable  */
    private mixed $columnAttributesCallback;

    /** @var callable  */
    private mixed $valueCallback;

    public function __construct(
        private string $name,
        private string $label,
        private ?string $entityAlias = null,
        private bool $sortable = false,
        private bool $filterable = false,
        private int $order = 0,
        private ?string $route = null,
        private array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        ?string $routeLocale = null,
        private ?string $class = null,
        private ?string $iconClass = null,
        ?callable $valueCallback = null,
        private bool $html = false,
        private bool $showColumnLabel = true,
        private array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
    ) {
        if ($routeLocale !== null) {
            $this->routeAttributes['_locale'] = $routeLocale;
        }

        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
        $this->valueCallback = $valueCallback;
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
        ?callable $routeAttributesCallback = null,
        ?string $routeLocale = null,
        ?string $class = null,
        ?callable $valueCallback = null,
        bool $html = false,
        array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
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
            columnAttributes: $columnAttributes,
            columnAttributesCallback: $columnAttributesCallback,
        );
    }

    public static function createMethodColumn(
        string $name,
        string $label,
        int $order,
        ?string $route = null,
        array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        ?string $routeLocale = null,
        ?string $class = null,
        bool $html = false,
        array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
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
            columnAttributes: $columnAttributes,
            columnAttributesCallback: $columnAttributesCallback,
        );
    }

    public static function createActionColumn(
        string $label,
        int $order = 0,
        ?string $route = null,
        array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        ?string $routeLocale = null,
        ?string $class = null,
        ?string $iconClass = null,
        ?callable $valueCallback = null,
        array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
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
            valueCallback: $valueCallback,
            showColumnLabel: false,
            columnAttributes: $columnAttributes,
            columnAttributesCallback: $columnAttributesCallback,
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
            return call_user_func($this->routeAttributesCallback, $entity, $this->routeAttributes);
        }

        return $this->routeAttributes;
    }

    public function getColumnAttributes(object $entity): array
    {
        if ($this->columnAttributesCallback !== null) {
            return call_user_func($this->columnAttributesCallback, $entity, $this->columnAttributes);
        }

        return $this->columnAttributes;
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
