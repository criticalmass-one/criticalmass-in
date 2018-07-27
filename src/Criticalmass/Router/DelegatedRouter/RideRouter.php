<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Entity\Ride;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RideRouter extends AbstractDelegatedRouter
{
    /** @param Ride $ride */
    public function generate(RouteableInterface $ride, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if ($ride->hasSlug()) {
            $routeName = 'caldera_criticalmass_ride_slug';
        } else {
            $routeName = 'caldera_criticalmass_ride_show';
        }

        $parameterList = array_merge($this->generateParameterList($ride, $routeName), $parameters);

        return $this->router->generate($routeName, $parameterList, $referenceType);
    }
}
