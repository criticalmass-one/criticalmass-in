<?php

namespace Caldera\Bundle\CriticalmassLiveBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $this->getMetadata()
            ->setDescription('Live-Tracking für Critical-Mass-Touren');

        $startDateTime = new \DateTime();
        $startDateTime->sub(new \DateInterval('PT6H'));

        $endDateTime = new \DateTime();
        $endDateTime->add(new \DateInterval('P2M'));

        $rides = $this->getRideRepository()->findRidesAndCitiesInInterval($startDateTime, $endDateTime);

        return $this->render(
            'CalderaCriticalmassLiveBundle:Default:index.html.twig',
            [
                'rides' => $rides
            ]
        );
    }
}
