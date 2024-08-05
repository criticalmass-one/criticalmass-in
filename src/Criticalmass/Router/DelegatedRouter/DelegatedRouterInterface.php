<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface DelegatedRouterInterface
{
    public function generate(
        RouteableInterface $routeable,
        string $routeName = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string;

    public function supports(RouteableInterface $routeable): bool;

    public function setObjectRouter(ObjectRouterInterface $objectRouter): DelegatedRouterInterface;

    public function getRouteParameter(RouteableInterface $routeable, string $variableName): ?string;
}
