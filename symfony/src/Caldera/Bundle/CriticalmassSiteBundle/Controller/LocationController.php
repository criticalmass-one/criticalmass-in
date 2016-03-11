<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationController extends AbstractController
{
    public function listlocationsAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $locations = $this->getLocationRepository()->findAll();

        return $this->render(
            'CalderaCriticalmassDesktopBundle:Location:list.html.twig',
            [
                'locations' => $locations
            ]
        );
    }

    public function showAction(Request $request, $citySlug, $locationSlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $location = $this->getLocationRepository()->findOneBySlug($locationSlug);

        if (!$location) {
            throw new NotFoundHttpException();
        }

        $rides = $this->getRideRepository()->findRidesByLocation($location);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Location:show.html.twig',
            [
                'location' => $location,
                'rides' => $rides
            ]
        );
    }
}
