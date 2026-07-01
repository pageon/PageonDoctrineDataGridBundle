<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class DataGrid
{
    public string $noResultsMessage;

    private const null KNP_PAGINATOR_DEFAULT = null;

    /** @var callable|null */
    public mixed $rowAttributesCallback;

    public function __construct(
        public string $queryBuilderAlias,
        string|\Stringable $noResultsMessage = 'No results',
        public ?string $pageParameterName = self::KNP_PAGINATOR_DEFAULT,
        public ?string $sortFieldParameterName = self::KNP_PAGINATOR_DEFAULT,
        public ?string $sortDirectionParameterName = self::KNP_PAGINATOR_DEFAULT,
        public ?string $filterFieldParameterName = self::KNP_PAGINATOR_DEFAULT,
        public ?string $filterValueParameterName = self::KNP_PAGINATOR_DEFAULT,
        public ?bool $distinct = self::KNP_PAGINATOR_DEFAULT,
        public ?string $pageOutOfRange = self::KNP_PAGINATOR_DEFAULT,
        public ?int $defaultLimit = self::KNP_PAGINATOR_DEFAULT,
        public array $rowAttributes = [],
        ?callable $rowAttributesCallback = null,
    ) {
        $this->noResultsMessage = (string) $noResultsMessage;
        $this->rowAttributesCallback = $rowAttributesCallback;
    }

    public function getPaginatorOptions(): array
    {
        return array_filter(
            [
                'pageParameterName' => $this->pageParameterName,
                'sortFieldParameterName' => $this->sortFieldParameterName,
                'sortDirectionParameterName' => $this->sortDirectionParameterName,
                'filterFieldParameterName' => $this->filterFieldParameterName,
                'filterValueParameterName' => $this->filterValueParameterName,
                'distinct' => $this->distinct,
                'pageOutOfRange' => $this->pageOutOfRange,
                'defaultLimit' => $this->defaultLimit,
            ],
            fn(mixed $value): bool => $value !== null
        );
    }
}