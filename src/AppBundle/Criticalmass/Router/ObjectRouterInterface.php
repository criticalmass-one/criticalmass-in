<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Router;

use AppBundle\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use AppBundle\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface ObjectRouterInterface
{
    public function generate(RouteableInterface $routeable, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;

    public function addDelegatedRouter(DelegatedRouterInterface $delegatedRouter): ObjectRouterInterface;
}
