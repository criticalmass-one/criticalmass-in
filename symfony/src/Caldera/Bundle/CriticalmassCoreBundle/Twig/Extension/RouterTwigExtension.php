<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;

class RouterTwigExtension extends \Twig_Extension
{
    protected $router;

    public function __construct(RouterInterface $router)
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

    public function objectPath($object)
    {
        echo 'FEWFWEF';
    }

    public function getName()
    {
        return 'router_extension';
    }
}