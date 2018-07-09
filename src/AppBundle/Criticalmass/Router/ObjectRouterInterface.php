<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Router;

use AppBundle\EntityInterface\RouteableInterface;

interface ObjectRouterInterface
{
    public function generate(RouteableInterface $routeable, string $routeName = null): string;
}
