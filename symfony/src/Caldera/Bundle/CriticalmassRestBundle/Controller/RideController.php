<?php

namespace Caldera\Bundle\CriticalmassRestBundle\Controller;

use Caldera\Bundle\CalderaBundle\Traits\RepositoryTrait;
use Caldera\Bundle\CriticalmassCoreBundle\Traits\UtilTrait;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

class RideController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    public function showAction(string $citySlug, string $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    public function showCurrentAction(string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);

        $ride = $this->getRideRepository()->findCurrentRideForCity($city);

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
