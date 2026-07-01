<?php

namespace Pageon\DoctrineDataGridBundle\Column;

final class Column
{
    public readonly string $label;
    public readonly array $routeAttributes;

    /** @var callable|null */
    private mixed $routeAttributesCallback;

    /** @var callable|null */
    private mixed $columnAttributesCallback;

    /** @var callable|null */
    private mixed $valueCallback;

    public string $fullName {
        get => $this->entityAlias === null ? $this->name : $this->entityAlias . '.' . $this->name;
    }

    public bool $hasValueCallback {
        get => $this->valueCallback !== null;
    }

    public function __construct(
        public readonly string $name,
        string|\Stringable $label,
        public readonly ?string $entityAlias = null,
        public readonly bool $sortable = false,
        public readonly bool $filterable = false,
        public readonly int $order = 0,
        public readonly ?string $route = null,
        array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        ?string $routeLocale = null,
        public readonly ?string $class = null,
        public readonly ?string $iconClass = null,
        ?callable $valueCallback = null,
        public readonly bool $html = false,
        public readonly bool $showColumnLabel = true,
        private readonly array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
    ) {
        $this->label = (string) $label;

        if ($routeLocale !== null) {
            $routeAttributes['_locale'] = $routeLocale;
        }
        $this->routeAttributes = $routeAttributes;

        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
        $this->valueCallback = $valueCallback;
    }

    public static function createPropertyColumn(
        string $name,
        string|\Stringable $label,
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
        string|\Stringable $label,
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
        string|\Stringable $label,
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
            name: (string) $label,
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

    public function getValue(mixed $value, mixed $row, string $columnName): mixed
    {
        if ($this->valueCallback !== null) {
            return call_user_func($this->valueCallback, $value, $row, $columnName);
        }

        return $value;
    }
}