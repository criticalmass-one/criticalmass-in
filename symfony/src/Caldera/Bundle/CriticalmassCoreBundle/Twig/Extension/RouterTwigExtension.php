<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Twig\Extension;

use Caldera\Bundle\CriticalmassCoreBundle\Router\ObjectRouter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouterTwigExtension extends \Twig_Extension
{
    /** @var ObjectRouter $router */
    protected $router;

    public function __construct(ObjectRouter $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('objectPath', [$this, 'objectPath'], array(
                'is_safe' => array('raw')
            )),
        ];
    }

    public function objectPath($object, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate($object, $referenceType);
    }

    public function getName()
    {
        return 'router_extension';
    }
}