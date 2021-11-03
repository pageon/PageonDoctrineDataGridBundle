<?php

namespace Pageon\DoctrineDataGridBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class TwigExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $twigLoaderDefinition = $container->hasDefinition('twig.loader.native_filesystem')
            ? $container->getDefinition('twig.loader.native_filesystem') : null;
        if (!$twigLoaderDefinition instanceof Definition) {
            return;
        }

        $templatesPath = __DIR__ . '/../../templates';
        $twigLoaderDefinition->addMethodCall('addPath', [$templatesPath, 'PageonDoctrineDataGrid']);
        $twigLoaderDefinition->addMethodCall('addPath', [$templatesPath, '!PageonDoctrineDataGrid']);
    }
}
