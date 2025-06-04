<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Entity\Ride;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RideRouter extends AbstractDelegatedRouter
{
    /** @param Ride $ride */
    public function generate(
        RouteableInterface $ride,
        string $routeName = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $parameterList = array_merge($this->generateParameterList($ride, $routeName), $parameters);

        return $this->router->generate($routeName, $parameterList, $referenceType);
    }

    /** @var Ride $ride */
    public function getRouteParameter(RouteableInterface $ride, string $variableName): ?string
    {
        if ($variableName === 'rideIdentifier') {
            if ($ride->hasSlug()) {
                return $ride->getSlug();
            } else {
                return $ride->getDateTime()->format('Y-m-d');
            }
        }

        return parent::getRouteParameter($ride, $variableName);
    }
}
