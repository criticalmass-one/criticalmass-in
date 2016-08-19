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
        $endDateTime = new \DateTime();

        $interval = new \DateInterval('P3D');
        $endDateTime->add($interval);

        $rides = $this->getRideRepository()->findRidesInInterval($startDateTime, $endDateTime);

        return $this->render(
            'CalderaCriticalmassLiveBundle:Default:index.html.twig',
            [
                'rides' => $rides
            ]
        );
    }
}
