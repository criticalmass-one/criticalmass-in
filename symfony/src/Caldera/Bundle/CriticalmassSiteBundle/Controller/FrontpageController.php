<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Timeline;
use Symfony\Component\HttpFoundation\Request;

class FrontpageController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $currentRides = $this->getRideRepository()->findFrontpageRides();

        $this->getMetadata()
            ->setDescription('criticalmass.in sammelt Fotos, Tracks und Informationen über weltweite Critical-Mass-Touren')
            ->setKeywords('Critical Mass, Tracks, Live-Tracking, Tracking');

        /**
         * @var Timeline $timeline
         */
        $timelineContent = $this
            ->get('caldera.criticalmass.timeline')
            ->execute()
            ->getTimelineContent();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Frontpage:index.html.twig',
            [
                'currentRides' => $currentRides,
                'timelineContent' => $timelineContent
            ]
        );
    }
}
