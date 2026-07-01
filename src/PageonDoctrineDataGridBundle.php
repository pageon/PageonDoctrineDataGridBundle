<?php

namespace Pageon\DoctrineDataGridBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PageonDoctrineDataGridBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
