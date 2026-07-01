<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class DataGridActionColumn
{
    public ?string $label;

    /** @var callable|null */
    public mixed $routeAttributesCallback;

    /** @var callable|null */
    public mixed $columnAttributesCallback;

    /** @var callable|null */
    public mixed $valueCallback;

    public function __construct(
        public string $route,
        public array $routeAttributes = [],
        ?callable $routeAttributesCallback = null,
        public ?string $routeLocale = null,
        public int $order = 1,
        string|\Stringable|null $label = null,
        public string $class = 'btn btn-primary btn-sm float-end',
        public ?string $iconClass = null,
        ?callable $valueCallback = null,
        public ?string $requiredRole = null,
        public array $columnAttributes = [],
        ?callable $columnAttributesCallback = null,
    ) {
        $this->label = $label !== null ? (string) $label : null;
        $this->valueCallback = $valueCallback;
        $this->routeAttributesCallback = $routeAttributesCallback;
        $this->columnAttributesCallback = $columnAttributesCallback;
    }
}