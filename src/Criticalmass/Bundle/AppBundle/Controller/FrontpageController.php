<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\SeoPage\SeoPage;
use Criticalmass\Component\Timeline\CachedTimeline;
use Criticalmass\Component\Timeline\Timeline;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontpageController extends AbstractController
{
    public function indexAction(Request $request, SeoPage $seoPage): Response
    {
        $seoPage->setDescription('criticalmass.in sammelt Fotos, Tracks und Informationen über weltweite Critical-Mass-Touren');

        $rideList = $this->getFrontpageRideList();
        $frontpageTeaserList = $this->getFrontpageTeaserRepository()->findForFrontpage();

        $endDateTime = new \DateTime();
        $startDateTime = new \DateTime();
        $monthInterval = new \DateInterval('P1M');
        $startDateTime->sub($monthInterval);

        /**
         * @var Timeline $timeline
         */
        $timelineContent = $this
            ->get(CachedTimeline::class)
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContent();

        return $this->render(
            'AppBundle:Frontpage:index.html.twig',
            [
                'timelineContent' => $timelineContent,
                'rideList' => $rideList,
                'frontpageTeaserList' => $frontpageTeaserList,
            ]
        );
    }

    protected function getFrontpageRideList(): array
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
