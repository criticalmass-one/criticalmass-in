<?php declare(strict_types=1);

namespace App\Criticalmass\Router;

use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface ObjectRouterInterface
{
    public function generate(RouteableInterface $routeable, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;

    public function addDelegatedRouter(DelegatedRouterInterface $delegatedRouter): ObjectRouterInterface;

    public function getRouteParameter(RouteableInterface $routeable, string $variableName): ?string;
}
