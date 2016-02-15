<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class FrontpageController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $currentRides = $this->getRideRepository()->findFrontpageRides();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Frontpage:index.html.twig',
            [
                'currentRides' => $currentRides
            ]
        );
    }
}
