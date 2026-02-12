<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Model\Frontpage\RideList\MonthList;
use App\Repository\FrontpageTeaserRepository;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontpageController extends AbstractController
{
    #[Route('/', name: 'caldera_criticalmass_frontpage', priority: 180)]
    public function indexAction(
        FrontpageTeaserRepository $frontpageTeaserRepository,
        SeoPageInterface $seoPage
    ): Response {
        $seoPage->setDescription('criticalmass.in sammelt Fotos, Tracks und Informationen über weltweite Critical-Mass-Touren');

        $frontpageTeaserList = $frontpageTeaserRepository->findForFrontpage();

        return $this->render('Frontpage/index.html.twig', [
            'frontpageTeaserList' => $frontpageTeaserList,
            'apiUrl' => '/api/timeline',
        ]);
    }

    public function rideListAction(RideRepository $rideRepository): Response
    {
        $monthList = new MonthList();

        foreach ($rideRepository->findFrontpageRides() as $ride) {
            $monthList->addRide($ride);
        }

        foreach ($monthList as $month) {
            $month->sort();
        }

        return $this->render('Frontpage/_ride_list.html.twig', [
            'rideList' => $monthList,
        ]);
    }
}
