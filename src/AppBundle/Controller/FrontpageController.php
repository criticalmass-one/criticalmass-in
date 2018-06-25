<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\SeoPage\SeoPage;
use AppBundle\Criticalmass\Timeline\CachedTimeline;
use AppBundle\Criticalmass\Timeline\Timeline;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontpageController extends AbstractController
{
    public function indexAction(SeoPage $seoPage, CachedTimeline $cachedTimeline): Response
    {
        $seoPage->setDescription('criticalmass.in sammelt Fotos, Tracks und Informationen über weltweite Critical-Mass-Touren');

        $rideList = $this->getFrontpageRideList();
        $frontpageTeaserList = $this->getFrontpageTeaserRepository()->findForFrontpage();

        $endDateTime = new \DateTime();
        $startDateTime = new \DateTime();
        $monthInterval = new \DateInterval('P1M');
        $startDateTime->sub($monthInterval);

        $timelineContent = $cachedTimeline
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContent();

        return $this->render('AppBundle:Frontpage:index.html.twig', [
            'timelineContent' => $timelineContent,
            'rideList' => $rideList,
            'frontpageTeaserList' => $frontpageTeaserList,
        ]);
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

    public function introAction(): Response
    {
        return $this->render('AppBundle:Frontpage:intro.html.twig');
    }
}
