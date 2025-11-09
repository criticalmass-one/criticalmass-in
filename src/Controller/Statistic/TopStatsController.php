<?php declare(strict_types=1);

namespace App\Controller\Statistic;

use App\Controller\AbstractController;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TopStatsController extends AbstractController
{
    #[Route(
        '/statistic/top',
        name: 'caldera_criticalmass_statistic_topten',
        priority: 140
    )]
    public function topStatsAction(RideRepository $rideRepository): Response
    {
        return $this->render('Statistic/top.html.twig', [
            'participationList' => $rideRepository->findMostPopularRides(),
            'durationList' => $rideRepository->findLongestDurationRides(),
            'distanceList' => $rideRepository->findLongestDistanceRides(),
        ]);
    }
}
