<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use Doctrine\Common\Annotations\Reader;

abstract class AbstractParameterResolver implements ParameterResolverInterface
{
    /** @var Reader $annotationReader*/
    protected $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }
}