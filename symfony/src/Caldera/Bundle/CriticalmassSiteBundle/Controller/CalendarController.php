<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $dateTime = new \DateTime();

        $rides = $this->getRideRepository()->findRidesByDateTimeMonth($dateTime);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Calendar:index.html.twig',
            [
                'rides' => $rides
            ]
        );
    }
}
