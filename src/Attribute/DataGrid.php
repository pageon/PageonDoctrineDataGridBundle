<?php

namespace Pageon\DoctrineDataGridBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class DataGrid
{
    private const KNP_PAGINATOR_DEFAULT = null;

    /** @var callable  */
    private mixed $rowAttributesCallback;

    public function __construct(
        private string $queryBuilderAlias,
        private string $noResultsMessage = 'No results',
        private ?string $pageParameterName = self::KNP_PAGINATOR_DEFAULT,
        private ?string $sortFieldParameterName = self::KNP_PAGINATOR_DEFAULT,
        private ?string $sortDirectionParameterName = self::KNP_PAGINATOR_DEFAULT,
        private ?string $filterFieldParameterName = self::KNP_PAGINATOR_DEFAULT,
        private ?string $filterValueParameterName = self::KNP_PAGINATOR_DEFAULT,
        private ?bool $distinct = self::KNP_PAGINATOR_DEFAULT,
        private ?string $pageOutOfRange = self::KNP_PAGINATOR_DEFAULT,
        private ?int $defaultLimit = self::KNP_PAGINATOR_DEFAULT,
        private array $rowAttributes = [],
        ?callable $rowAttributesCallback = null,
    ) {
        $this->rowAttributesCallback = $rowAttributesCallback;
    }

    public function getQueryBuilderAlias(): string
    {
        return $this->queryBuilderAlias;
    }

    public function getNoResultsMessage(): string
    {
        return $this->noResultsMessage;
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
            static function (mixed $value): bool {
                return $value !== null;
            }
        );
    }

    public function getRowAttributes(): array
    {
        return $this->rowAttributes;
    }

    public function getRowAttributesCallback(): ?callable
    {
        return $this->rowAttributesCallback;
    }
}
