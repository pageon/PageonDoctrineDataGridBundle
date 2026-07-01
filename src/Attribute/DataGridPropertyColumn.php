<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class DataGridPropertyColumn
{
    public ?string $label;

    /** @var callable|null */
    public mixed $routeAttributesCallback;

    /** @var callable|null */
    public mixed $columnAttributesCallback;

    /** @var callable|null */
    public mixed $valueCallback;

    public function __construct(
        public bool $sortable = false,
        public bool $filterable = false,
        public int $order = 0,
        string|\Stringable|null $label = null,
        public ?string $class = null,
        ?callable $valueCallback = null,
        public bool $html = false,
        public ?string $route = null,
        public array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        public ?string $routeLocale = null,
        public ?string $routeRole = null,
        public array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
    ) {
        $this->label = $label !== null ? (string) $label : null;
        $this->valueCallback = $valueCallback;
        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
    }
}