<?php

namespace Caldera\Bundle\CalderaBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Timeline\Timeline;
use Symfony\Component\HttpFoundation\Request;

class FrontpageController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $this->getMetadata()
            ->setDescription('criticalmass.in sammelt Fotos, Tracks und Informationen über weltweite Critical-Mass-Touren')
            ->setKeywords('Critical Mass, Tracks, Live-Tracking, Tracking');

        $rideList = $this->getFrontpageRideList();

        $endDateTime = new \DateTime();
        $startDateTime = new \DateTime();
        $monthInterval = new \DateInterval('P1M');
        $startDateTime->sub($monthInterval);

        /**
         * @var Timeline $timeline
         */
        $timelineContent = $this
            ->get('caldera.criticalmass.timeline.cached')
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContent();

        return $this->render(
            'CalderaBundle:Frontpage:index.html.twig',
            [
                'timelineContent' => $timelineContent,
                'rideList' => $rideList
            ]
        );
    }

    protected function getFrontpageRideList()
    {
        $rides = $this->getRideRepository()->findFrontpageRides();

        $rideList = [];

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $rideDate = $ride->getFormattedDate();
            $citySlug = $ride->getCity()->getSlug();

            if (!array_key_exists($rideDate, $rideList)) {
                $rideList[$rideDate] = [];
            }

            $rideList[$rideDate][$citySlug] = $ride;
        }

        return $rideList;
    }
}
