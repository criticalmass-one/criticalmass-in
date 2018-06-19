<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Statistic;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TopStatsController extends AbstractController
{
    public function topStatsAction(): Response
    {
        return $this->render('AppBundle:Statistic:top.html.twig', [
            'participationList' => $this->getRideRepository()->findMostPopularRides(),
            'durationList' => $this->getRideRepository()->findLongestDurationRides(),
            'distanceList' => $this->getRideRepository()->findLongestDistanceRides(),
        ]);
    }
}
