<?php

namespace Pageon\DoctrineDataGridBundle\DataGrid;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid as DataGridAttribute;
use Knp\Component\Pager\PaginatorInterface;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;
use Pageon\DoctrineDataGridBundle\Column\Column;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\ValueHolderInterface;
use ReflectionClass;
use Stringable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\VarExporter\LazyObjectInterface;

final class DataGridFactory
{
    private ?string $defaultPageParameterName = null;

    public function __construct(
        private PaginatorInterface $paginator,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private ?AuthorizationCheckerInterface $authorizationChecker = null,
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

        $queryBuilder = $repository->createQueryBuilder($dataGridInfo->getQueryBuilderAlias())
            ->select($dataGridInfo->getQueryBuilderAlias());
        if ($queryBuilderCallback !== null) {
            $queryBuilderCallback($queryBuilder);
        }

        $page = $this->requestStack->getMainRequest()->query->getInt($this->getDefaultPageParameterName(), 1);
        $columns = $this->getColumns(
            $classInfo,
            $dataGridInfo->getQueryBuilderAlias()
        );
        array_push($columns, ...$extraColumns);

        return new DataGrid(
            $this->paginator->paginate($queryBuilder, $page, $limit),
            $columns,
            $dataGridInfo->getNoResultsMessage(),
            $dataGridInfo->getRowAttributes(),
            $dataGridInfo->getRowAttributesCallback(),
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
        $columns = $this->getColumns(
            $classInfo,
            $dataGridInfo->getQueryBuilderAlias()
        );
        array_push($columns, ...$extraColumns);

        return new DataGrid(
            $this->paginator->paginate($data, $page, $limit),
            $columns,
            $dataGridInfo->getNoResultsMessage(),
            $rowAttributes,
            $rowAttributesCallback
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

            /** @var DataGridPropertyColumn $columnProperties */
            $columnProperties = $attribute->newInstance();

            $showLink = true;
            if (
                $this->authorizationChecker !== null
                && $columnProperties->getRouteRole() !== null
                && !$this->authorizationChecker->isGranted($columnProperties->getRouteRole())
            ) {
                $showLink = false;
            }

            $columns[] = Column::createPropertyColumn(
                name: $property->getName(),
                label: $columnProperties->getLabel() ?? $property->getName(),
                entityAlias: $className,
                sortable: $columnProperties->isSortable(),
                filterable: $columnProperties->isFilterable(),
                order: $columnProperties->getOrder(),
                route: $showLink ? $columnProperties->getRoute() : null,
                routeAttributes: $showLink ? $columnProperties->getRouteAttributes() : null,
                routeAttributesCallback: $showLink ? $columnProperties->getRouteAttributesCallback() : null,
                routeLocale: $showLink ? $columnProperties->getRouteLocale() : null,
                class: $columnProperties->getClass(),
                valueCallback: $columnProperties->getValueCallback(),
                html: $columnProperties->isHtml(),
                columnAttributes: $columnProperties->getColumnAttributes(),
                columnAttributesCallback: $columnProperties->getColumnAttributesCallback(),
            );
        }

        foreach ($classInfo->getMethods() as $method) {
            $attribute = $method->getAttributes(DataGridMethodColumn::class)[0] ?? null;
            if ($attribute === null) {
                continue;
            }

            /** @var DataGridMethodColumn $methodProperties */
            $methodProperties = $attribute->newInstance();

            $showLink = true;
            if (
                $this->authorizationChecker !== null
                && $methodProperties->getRouteRole() !== null
                && !$this->authorizationChecker->isGranted($methodProperties->getRouteRole())
            ) {
                $showLink = false;
            }

            $columns[] = Column::createMethodColumn(
                name: $method->getName(),
                label: $methodProperties->getLabel() ?? $method->getName(),
                order: $methodProperties->getOrder(),
                route: $showLink ? $methodProperties->getRoute() : null,
                routeAttributes: $showLink ? $methodProperties->getRouteAttributes() : null,
                routeAttributesCallback: $showLink ? $methodProperties->getRouteAttributesCallback() : null,
                routeLocale: $showLink ? $methodProperties->getRouteLocale() : null,
                class: $methodProperties->getClass(),
                html: $methodProperties->isHtml(),
                columnAttributes: $methodProperties->getColumnAttributes(),
                columnAttributesCallback: $methodProperties->getColumnAttributesCallback(),
            );
        }

        foreach ($classInfo->getAttributes(DataGridActionColumn::class) as $action) {
            /** @var DataGridActionColumn $actionProperties */
            $actionProperties = $action->newInstance();
            if (
                $this->authorizationChecker !== null
                && $actionProperties->getRequiredRole() !== null
                && !$this->authorizationChecker->isGranted($actionProperties->getRequiredRole())
            ) {
                continue;
            }
            $columns[] = Column::createActionColumn(
                label: $actionProperties->getLabel(),
                order: $actionProperties->getOrder(),
                route: $actionProperties->getRoute(),
                routeAttributes: $actionProperties->getRouteAttributes(),
                routeAttributesCallback: $actionProperties->getRouteAttributesCallback(),
                routeLocale: $actionProperties->getRouteLocale(),
                class: $actionProperties->getClass(),
                iconClass: $actionProperties->getIconClass(),
                valueCallback: $actionProperties->getValueCallback(),
                columnAttributes: $actionProperties->getColumnAttributes(),
                columnAttributesCallback: $actionProperties->getColumnAttributesCallback(),
            );
        }

        usort($columns, static function (Column $a, Column $b) {
            return $a->getOrder() <=> $b->getOrder();
        });


        return $columns;
    }

    private function getDefaultPageParameterName(): string
    {
        if ($this->defaultPageParameterName !== null) {
            return $this->defaultPageParameterName;
        }

        $paginator = $this->paginator;
        if ($paginator instanceof LazyLoadingInterface && !$paginator->isProxyInitialized()) {
            $paginator->initializeProxy();
        }
        if ($paginator instanceof ValueHolderInterface) {
            $paginator = $paginator->getWrappedValueHolderValue();
        }
        if ($paginator instanceof LazyObjectInterface) {
            $paginator = $paginator->initializeLazyObject();
        }

        $paginatorInfo = new ReflectionClass($paginator);
        $defaultOptionsProperty = $paginatorInfo->getProperty('defaultOptions');
        $defaultOptionsProperty->setAccessible(true);

        $defaultOptions = $defaultOptionsProperty->getValue($paginator);
        $this->defaultPageParameterName = $defaultOptions[PaginatorInterface::PAGE_PARAMETER_NAME];

        return $this->defaultPageParameterName;
    }
}
