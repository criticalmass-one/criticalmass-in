<?php declare(strict_types=1);

namespace App\Controller\Statistic;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TopStatsController extends AbstractController
{
    public function topStatsAction(): Response
    {
        return $this->render('Statistic/top.html.twig', [
            'participationList' => $this->getRideRepository()->findMostPopularRides(),
            'durationList' => $this->getRideRepository()->findLongestDurationRides(),
            'distanceList' => $this->getRideRepository()->findLongestDistanceRides(),
        ]);
    }
}
