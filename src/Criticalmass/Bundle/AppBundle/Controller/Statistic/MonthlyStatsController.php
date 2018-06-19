<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Statistic;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MonthlyStatsController extends AbstractController
{
    public function listRidesAction(int $year = null, int $month = null): Response
    {
        if (!$year || $month) {
            $dateTime = new \DateTime();

            $year = (int) $dateTime->format('Y');
            $month = (int) $dateTime->format('m');
        } else {
            $dateTimeSpec = sprintf('%d-%d-01', $year, $month);
            $dateTime = new \DateTime($dateTimeSpec);
        }

        $rides = $this->getRideRepository()->findEstimatedRides($year, $month);

        return $this->render('AppBundle:Statistic:list_rides.html.twig', [
            'rides' => $rides,
            'dateTime' => $dateTime,
        ]);
    }
}
