<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class DataGridMethodColumn
{
    public ?string $label;

    /** @var callable|null */
    public mixed $routeAttributesCallback;

    /** @var callable|null */
    public mixed $columnAttributesCallback;

    public function __construct(
        public int $order = 0,
        string|\Stringable|null $label = null,
        public ?string $class = null,
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
        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
    }
}