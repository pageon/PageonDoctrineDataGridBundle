<?php

namespace Pageon\DoctrineDataGridBundle;

use Pageon\DoctrineDataGridBundle\DependencyInjection\PageonDoctrineDataGridBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PageonDoctrineDataGridBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new PageonDoctrineDataGridBundleExtension();
    }
}
