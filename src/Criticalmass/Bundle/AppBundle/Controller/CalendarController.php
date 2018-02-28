<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Calendar\EventProvider\RideProvider;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $this->getSeoPage()
            ->setDescription('Kalender-Übersicht über weltweitere Critical-Mass-Touren.')
        ;

        return $this->render(
            'AppBundle:Calendar:index.html.twig', [
                'time' => new \DateTime()
            ]
        );
    }
}
