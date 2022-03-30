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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
        ?int $limit = null
    ): DataGrid {
        $repository = $this->entityManager->getRepository($fullyQualifiedClassName);
        $classInfo = new ReflectionClass($fullyQualifiedClassName);
        /** @var DataGridAttribute $dataGridInfo */
        $dataGridInfo = ($classInfo->getAttributes(DataGridAttribute::class)[0] ?? null)->newInstance()
                        ?? throw new InvalidArgumentException('The entity needs to have the DataGrid attribute');

        $queryBuilder = $repository->createQueryBuilder($dataGridInfo->getQueryBuilderAlias())
            ->select($dataGridInfo->getQueryBuilderAlias());
        if ($queryBuilderCallback !== null) {
            $queryBuilderCallback($queryBuilder);
        }

        $page = $this->requestStack->getMainRequest()->query->getInt($this->getDefaultPageParameterName(), 1);

        return new DataGrid(
            $this->paginator->paginate($queryBuilder, $page, $limit),
            $this->getColumns(
                $classInfo,
                $dataGridInfo->getQueryBuilderAlias()
            ),
            $dataGridInfo->getNoResultsMessage()
        );
    }

    public function forArray(
        string $fullyQualifiedClassName,
        array $data = [],
        ?int $limit = null
    ): DataGrid {
        $classInfo = new ReflectionClass($fullyQualifiedClassName);
        /** @var DataGridAttribute $dataGridInfo */
        $dataGridInfo = ($classInfo->getAttributes(DataGridAttribute::class)[0] ?? null)->newInstance()
                        ?? throw new InvalidArgumentException('The entity needs to have the DataGrid attribute');

        $page = $this->requestStack->getMainRequest()->query->getInt($this->getDefaultPageParameterName(), 1);

        return new DataGrid(
            $this->paginator->paginate($data, $page, $limit),
            $this->getColumns(
                $classInfo,
                $dataGridInfo->getQueryBuilderAlias()
            ),
            $dataGridInfo->getNoResultsMessage()
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
            $columns[] = Column::createPropertyColumn(
                name: $property->getName(),
                label: $columnProperties->getLabel() ?? $property->getName(),
                entityAlias: $className,
                sortable: $columnProperties->isSortable(),
                filterable: $columnProperties->isFilterable(),
                order: $columnProperties->getOrder(),
                route: $columnProperties->getRoute(),
                routeAttributes: $columnProperties->getRouteAttributes(),
                routeAttributesCallback: $columnProperties->getRouteAttributesCallback(),
                routeLocale: $columnProperties->getRouteLocale(),
                class: $columnProperties->getClass(),
                valueCallback: $columnProperties->getValueCallback(),
                html: $columnProperties->isHtml(),
            );
        }

        foreach ($classInfo->getMethods() as $method) {
            $attribute = $method->getAttributes(DataGridMethodColumn::class)[0] ?? null;
            if ($attribute === null) {
                continue;
            }

            /** @var DataGridMethodColumn $columnProperties */
            $columnProperties = $attribute->newInstance();
            $columns[] = Column::createMethodColumn(
                name: $method->getName(),
                label: $columnProperties->getLabel() ?? $method->getName(),
                order: $columnProperties->getOrder(),
                route: $columnProperties->getRoute(),
                routeAttributes: $columnProperties->getRouteAttributes(),
                routeAttributesCallback: $columnProperties->getRouteAttributesCallback(),
                routeLocale: $columnProperties->getRouteLocale(),
                class: $columnProperties->getClass(),
                html: $columnProperties->isHtml(),
            );
        }

        foreach ($classInfo->getAttributes(DataGridActionColumn::class) as $action) {
            /** @var DataGridActionColumn $actionProperties */
            $actionProperties = $action->newInstance();
            if (
                $actionProperties->getRequiredRole() !== null
                && $this->authorizationChecker !== null
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

        $paginatorInfo = new ReflectionClass($paginator);
        $defaultOptionsProperty = $paginatorInfo->getProperty('defaultOptions');
        $defaultOptionsProperty->setAccessible(true);

        $defaultOptions = $defaultOptionsProperty->getValue($paginator);
        $this->defaultPageParameterName = $defaultOptions[PaginatorInterface::PAGE_PARAMETER_NAME];

        return $this->defaultPageParameterName;
    }
}
