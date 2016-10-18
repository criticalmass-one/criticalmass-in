<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class CalendarController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $dateTime = new \DateTime();

        $rides = $this->getRideRepository()->findRidesByDateTimeMonth($dateTime);

        $days = [];

        foreach ($rides as $ride) {
            $days[$ride->getFormattedDate()][] = $ride;
        }

        $this->getMetadata()
            ->setDescription('Kalender-Übersicht über weltweitere Critical-Mass-Touren.')
            ->setDate(new \DateTime());

        return $this->render(
            'CalderaCriticalmassSiteBundle:Calendar:index.html.twig',
            [
                'days' => $days
            ]
        );
    }
}
