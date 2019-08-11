<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouterManager;

use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\EntityInterface\RouteableInterface;

interface DelegatedRouterManagerInterface
{
    public function setObjectRouter(ObjectRouterInterface $objectRouter): DelegatedRouterManagerInterface;
    public function addDelegatedRouter(DelegatedRouterInterface $delegatedRouter): DelegatedRouterManagerInterface;
    public function findDelegatedRouter(RouteableInterface $routeable): ?DelegatedRouterInterface;
}
