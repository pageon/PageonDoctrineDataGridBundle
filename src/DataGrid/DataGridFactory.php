<?php

namespace Pageon\DoctrineDataGridBundle\DataGrid;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Knp\Component\Pager\PaginatorInterface;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid as DataGridAttribute;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;
use Pageon\DoctrineDataGridBundle\Column\Column;
use ReflectionClass;
use Stringable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\VarExporter\LazyObjectInterface;

final class DataGridFactory
{
    private ?string $defaultPageParameterName = null;

    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly ?AuthorizationCheckerInterface $authorizationChecker = null,
    ) {
    }

    public function forEntity(
        string $fullyQualifiedClassName,
        ?callable $queryBuilderCallback = null,
        ?int $limit = null,
        Column ...$extraColumns
    ): DataGrid {
        $repository = $this->entityManager->getRepository($fullyQualifiedClassName);
        $classInfo = new ReflectionClass($fullyQualifiedClassName);
        /** @var DataGridAttribute $dataGridInfo */
        $dataGridInfo = ($classInfo->getAttributes(DataGridAttribute::class)[0] ?? null)?->newInstance()
                        ?? throw new InvalidArgumentException('The entity needs to have the DataGrid attribute');

        $queryBuilder = $repository->createQueryBuilder($dataGridInfo->queryBuilderAlias)
            ->select($dataGridInfo->queryBuilderAlias);
        if ($queryBuilderCallback !== null) {
            $queryBuilderCallback($queryBuilder);
        }

        $page = $this->requestStack->getMainRequest()->query->getInt($this->getDefaultPageParameterName(), 1);
        $columns = $this->getColumns($classInfo, $dataGridInfo->queryBuilderAlias);
        array_push($columns, ...$extraColumns);

        return new DataGrid(
            $this->paginator->paginate($queryBuilder, $page, $limit),
            $columns,
            $dataGridInfo->noResultsMessage,
            $dataGridInfo->rowAttributes,
            $dataGridInfo->rowAttributesCallback,
        );
    }

    /**
     * @param object[] $data
     * @param array{string?:int|float|Stringable} $rowAttributes
     */
    public function forArray(
        string $fullyQualifiedClassName,
        array $data = [],
        ?int $limit = null,
        array $rowAttributes = [],
        ?callable $rowAttributesCallback = null,
        Column ...$extraColumns,
    ): DataGrid {
        $classInfo = new ReflectionClass($fullyQualifiedClassName);
        /** @var DataGridAttribute $dataGridInfo */
        $dataGridInfo = ($classInfo->getAttributes(DataGridAttribute::class)[0] ?? null)?->newInstance()
                        ?? throw new InvalidArgumentException('The class needs to have the DataGrid attribute');

        $page = $this->requestStack->getMainRequest()->query->getInt($this->getDefaultPageParameterName(), 1);
        $columns = $this->getColumns($classInfo, $dataGridInfo->queryBuilderAlias);
        array_push($columns, ...$extraColumns);

        return new DataGrid(
            $this->paginator->paginate($data, $page, $limit),
            $columns,
            $dataGridInfo->noResultsMessage,
            $rowAttributes,
            $rowAttributesCallback,
        );
    }

    /**
     * @return Column[]
     */
    public function getColumns(ReflectionClass $classInfo, string $className): array
    {
        $columns = [];

        foreach ($classInfo->getProperties() as $property) {
            $attribute = $property->getAttributes(DataGridPropertyColumn::class)[0] ?? null;
            if ($attribute === null) {
                continue;
            }

            /** @var DataGridPropertyColumn $col */
            $col = $attribute->newInstance();

            $showLink = $col->routeRole === null
                || $this->authorizationChecker === null
                || $this->authorizationChecker->isGranted($col->routeRole);

            $columns[] = Column::createPropertyColumn(
                name: $property->getName(),
                label: $col->label ?? $property->getName(),
                entityAlias: $className,
                sortable: $col->sortable,
                filterable: $col->filterable,
                order: $col->order,
                route: $showLink ? $col->route : null,
                routeAttributes: $showLink ? $col->routeAttributes : [],
                routeAttributesCallback: $showLink ? $col->routeAttributesCallback : null,
                routeLocale: $showLink ? $col->routeLocale : null,
                class: $col->class,
                valueCallback: $col->valueCallback,
                html: $col->html,
                columnAttributes: $col->columnAttributes,
                columnAttributesCallback: $col->columnAttributesCallback,
            );
        }

        foreach ($classInfo->getMethods() as $method) {
            $attribute = $method->getAttributes(DataGridMethodColumn::class)[0] ?? null;
            if ($attribute === null) {
                continue;
            }

            /** @var DataGridMethodColumn $col */
            $col = $attribute->newInstance();

            $showLink = $col->routeRole === null
                || $this->authorizationChecker === null
                || $this->authorizationChecker->isGranted($col->routeRole);

            $columns[] = Column::createMethodColumn(
                name: $method->getName(),
                label: $col->label ?? $method->getName(),
                order: $col->order,
                route: $showLink ? $col->route : null,
                routeAttributes: $showLink ? $col->routeAttributes : [],
                routeAttributesCallback: $showLink ? $col->routeAttributesCallback : null,
                routeLocale: $showLink ? $col->routeLocale : null,
                class: $col->class,
                html: $col->html,
                columnAttributes: $col->columnAttributes,
                columnAttributesCallback: $col->columnAttributesCallback,
            );
        }

        foreach ($classInfo->getAttributes(DataGridActionColumn::class) as $action) {
            /** @var DataGridActionColumn $col */
            $col = $action->newInstance();
            if (
                $col->requiredRole !== null
                && $this->authorizationChecker !== null
                && !$this->authorizationChecker->isGranted($col->requiredRole)
            ) {
                continue;
            }
            $columns[] = Column::createActionColumn(
                label: $col->label,
                order: $col->order,
                route: $col->route,
                routeAttributes: $col->routeAttributes,
                routeAttributesCallback: $col->routeAttributesCallback,
                routeLocale: $col->routeLocale,
                class: $col->class,
                iconClass: $col->iconClass,
                valueCallback: $col->valueCallback,
                columnAttributes: $col->columnAttributes,
                columnAttributesCallback: $col->columnAttributesCallback,
            );
        }

        usort($columns, fn(Column $a, Column $b) => $a->order <=> $b->order);

        return $columns;
    }

    private function getDefaultPageParameterName(): string
    {
        if ($this->defaultPageParameterName !== null) {
            return $this->defaultPageParameterName;
        }

        $paginator = $this->paginator;
        if ($paginator instanceof LazyObjectInterface) {
            $paginator = $paginator->initializeLazyObject();
        }

        $paginatorInfo = new ReflectionClass($paginator);
        $defaultOptions = $paginatorInfo->getProperty('defaultOptions')->getValue($paginator);
        $this->defaultPageParameterName = $defaultOptions[PaginatorInterface::PAGE_PARAMETER_NAME];

        return $this->defaultPageParameterName;
    }
}