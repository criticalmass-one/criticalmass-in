<?php declare(strict_types=1);

namespace App\Criticalmass\Router;

use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ObjectRouter extends AbstractObjectRouter implements ObjectRouterInterface
{
    public function generate(RouteableInterface $routeable, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if (!$routeName) {
            $routeName = $this->getDefaultRouteName($routeable);
        }

        if ($delegatedRouter = $this->findDelegatedRouter($routeable)) {
            return $delegatedRouter->generate($routeable, $routeName, $parameters, $referenceType);
        }

        if ($routeName) {
            $parameterList = array_merge($this->generateParameterList($routeable, $routeName), $parameters);

            try {
                return $this->router->generate($routeName, $parameterList, $referenceType);
            } catch (InvalidParameterException $exception) {
                $delegatedRouter = $this->findDelegatedRouter($routeable);

                return $delegatedRouter->generate($routeable, $routeName, $parameters, $referenceType);
            }
        }
    }

    public function addDelegatedRouter(DelegatedRouterInterface $delegatedRouter): ObjectRouterInterface
    {
        $this->delegatedRouterList[] = $delegatedRouter->setObjectRouter($this);

        return $this;
    }

}
