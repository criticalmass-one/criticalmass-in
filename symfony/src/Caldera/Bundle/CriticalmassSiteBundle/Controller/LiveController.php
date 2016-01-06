<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;


use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;

class LiveController extends AbstractController
{
    public function indexAction()
    {
        /**
         * @var Ride $ride
         */
        $ride = $this->getRideRepository()->find(1);
        
        return $this->render(
            'CalderaCriticalmassSiteBundle:Live:index.html.twig',
            array(
                'ride' => $ride,
                'city' => $ride->getCity()
            )
        );
    }
}
