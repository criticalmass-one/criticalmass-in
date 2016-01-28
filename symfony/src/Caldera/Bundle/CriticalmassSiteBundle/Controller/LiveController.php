<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;


use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

class LiveController extends AbstractController
{
    public function indexAction(Request $request)
    {
        /**
         * @var Ride $rides
         */
        $rides = $this->getRideRepository()->findCurrentRides();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Live:index.html.twig',
            array(
                'rides' => $rides
            )
        );
    }

    public function cityAction(Request $request, $citySlug)
    {
        /**
         * @var City $city
         */
        $city = $this->getCheckedCity($citySlug);

        /**
         * @var Ride $ride
         */
        $ride = $this->getRideRepository()->findCurrentRideForCity($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Live:index.html.twig',
            array(
                'rides' => [$ride]
            )
        );
    }
}
