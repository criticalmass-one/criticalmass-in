<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use Doctrine\Common\Annotations\Reader;

abstract class AbstractParameterResolver implements ParameterResolverInterface
{
    public function __construct(protected Reader $annotationReader)
    {
    }
}