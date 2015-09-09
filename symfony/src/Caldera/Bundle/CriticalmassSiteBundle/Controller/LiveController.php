<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;


class LiveController extends AbstractController
{
    public function indexAction()
    {
        $rides = $this->getRideRepository()->findCurrentRides();
        
        return $this->render(
            'CalderaCriticalmassSiteBundle:Live:index.html.twig',
            array(
                'rides' => $rides
            )
        );
    }
}
