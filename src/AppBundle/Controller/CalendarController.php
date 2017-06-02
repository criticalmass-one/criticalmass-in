<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $dateTime = new \DateTime();

        $rides = $this->getRideRepository()->findRidesByDateTimeMonth($dateTime);

        $dayList = $this->createDaysList($dateTime);

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $dayList[$ride->getDateTime()->format('Y-m-d')][$ride->getId()] = $ride;
        }

        $this->getMetadata()
            ->setDescription('Kalender-Übersicht über weltweitere Critical-Mass-Touren.')
            ->setDate(new \DateTime());

        return $this->render(
            'AppBundle:Calendar:index.html.twig',
            [
                'dayList' => $dayList,
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
