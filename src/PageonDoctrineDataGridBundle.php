<?php

namespace Pageon\DoctrineDataGridBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use function dirname;

final class PageonDoctrineDataGridBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
