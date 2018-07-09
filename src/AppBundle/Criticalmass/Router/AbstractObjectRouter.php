<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Router;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractObjectRouter implements ObjectRouterInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var AnnotationReader $annotationReader */
    protected $annotationReader;

    public function __construct(RouterInterface $router, AnnotationReader $annotationReader)
    {
        $this->router = $router;
        $this->annotationReader = $annotationReader;
    }
}
