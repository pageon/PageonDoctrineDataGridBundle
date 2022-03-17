<?php

namespace Pageon\DoctrineDataGridBundle\Twig;

use Pageon\DoctrineDataGridBundle\DataGrid\DataGrid;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DataGridExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'pageon_datagrid',
                [$this, 'parseDataGrid'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html']
                ]
            )
        ];
    }

    public function parseDataGrid(
        Environment $twig,
        DataGrid $dataGrid,
        string $template = '@PageonDoctrineDataGrid/dataGrid.html.twig',
        array $parameters = []
    ): string {
        return $twig->render($template, ['dataGrid' => $dataGrid] + $parameters);
    }
}
