<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class FrontpageController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $currentRides = $this->getRideRepository()->findFrontpageRides();

        $this->getMetadata()
            ->setDescription('criticalmass.in sammelt Fotos, Tracks und Informationen über weltweite Critical-Mass-Touren')
            ->setKeywords('Critical Mass, Tracks, Live-Tracking, Tracking');

        return $this->render(
            'CalderaCriticalmassSiteBundle:Frontpage:index.html.twig',
            [
                'currentRides' => $currentRides
            ]
        );
    }
}
