<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Criticalmass\Timeline\TimelineInterface;
use App\Factory\FrontpageRideListFactory;
use App\Repository\FrontpageTeaserRepository;
use Symfony\Component\HttpFoundation\Response;

class FrontpageController extends AbstractController
{
    public function indexAction(
        FrontpageTeaserRepository $frontpageTeaserRepository,
        SeoPageInterface $seoPage,
        TimelineInterface $cachedTimeline
    ): Response {
        $seoPage->setDescription('criticalmass.in sammelt Fotos, Tracks und Informationen über weltweite Critical-Mass-Touren');

        $frontpageTeaserList = $frontpageTeaserRepository->findForFrontpage();

        $endDateTime = new \DateTime();
        $startDateTime = new \DateTime();
        $monthInterval = new \DateInterval('P1M');
        $startDateTime->sub($monthInterval);

        $timelineContentList = $cachedTimeline
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContentList();

        return $this->render('Frontpage/index.html.twig', [
            'timelineContentList' => $timelineContentList,
            'frontpageTeaserList' => $frontpageTeaserList,
        ]);
    }

    public function rideListAction(FrontpageRideListFactory $frontpageRideListFactory): Response
    {
        $monthList = $frontpageRideListFactory
            ->createList()
            ->sort()
            ->getMonthList()
        ;

        return $this->render('Frontpage/_ride_list.html.twig', [
            'rideList' => $monthList,
        ]);
    }
}
