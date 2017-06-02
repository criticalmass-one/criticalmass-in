<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class CalendarController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $dateTime = new \DateTime();

        $rides = $this->getRideRepository()->findRidesByDateTimeMonth($dateTime);

        $days = $this->createDaysList($dateTime);

        foreach ($rides as $ride) {
            $days[$ride->getFormattedDate()][] = $ride;
        }

        $this->getMetadata()
            ->setDescription('Kalender-Übersicht über weltweitere Critical-Mass-Touren.')
            ->setDate(new \DateTime());

        return $this->render(
            'AppBundle:Calendar:index.html.twig',
            [
                'days' => $days,
                'time' => new \DateTime()
            ]
        );
    }

    protected function createDaysList(\DateTime $dateTime): array
    {
        $day = new \DateTime($dateTime->format('Y-m-1'));
        $lastDay = new \DateTime($dateTime->format('Y-m-t'));
        $dayInterval = new \DateInterval('P1D');

        $dayList = [];

        while ($day <= $lastDay) {
            $dayList[$day->format('Y-m-d')] = [];

            $day->add($dayInterval);
        }

        return $dayList;
    }
}
