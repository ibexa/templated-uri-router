<?php

namespace Hautelook\TemplatedUriRouter\Routing\Generator;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Symfony\Component\Routing\Generator\UrlGenerator;

if (!class_exists('Symfony\Component\Routing\Generator\CompiledUrlGenerator')) {
    abstract class BcUrlGenerator extends UrlGenerator
    {
    }
} else {
    abstract class BcUrlGenerator extends CompiledUrlGenerator
    {
    }
}
