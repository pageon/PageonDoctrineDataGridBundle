<?php

namespace Pageon\DoctrineDataGridBundle;

use Pageon\DoctrineDataGridBundle\DependencyInjection\TwigExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PageonDoctrineDataGridBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TwigExtensionPass());
    }
}
