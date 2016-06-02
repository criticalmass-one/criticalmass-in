<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;


use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

class LiveController extends AbstractController
{
    public function indexAction(Request $request)
    {
        /**
         * @var Ride $rides
         */
        $rides = $this->getRideRepository()->findCurrentRides();

        $this->getMetadata()
            ->setDescription('Live dabei: Verfolge verschiedene Critical-Mass-Touren weltweit!');

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


        $this->getMetadata()
            ->setDescription('Live dabei: Schau dir an, wo sich die Critical Mass in '.$city->getCity().' gerade befindet!');

        return $this->render(
            'CalderaCriticalmassSiteBundle:Live:index.html.twig',
            array(
                'rides' => [$ride]
            )
        );
    }
}
