<?php declare(strict_types=1);

namespace App\Controller\Statistic;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MonthlyStatsController extends AbstractController
{
    public function listRidesAction(int $year = null, int $month = null): Response
    {
        $now = new \DateTime();

        if (!$year || !$month) {
            $dateTime = new \DateTimeImmutable();

            $year = (int) $dateTime->format('Y');
            $month = (int) $dateTime->format('m');
        } else {
            $dateTimeSpec = sprintf('%d-%d-01', $year, $month);
            $dateTime = new \DateTimeImmutable($dateTimeSpec);
        }

        $rides = $this->getRideRepository()->findEstimatedRides($year, $month);

        $month = new \DateInterval('P1M');

        return $this->render('Statistic/list_rides.html.twig', [
            'rides' => $rides,
            'dateTime' => $dateTime,
            'previousDateTime' => $dateTime->sub($month),
            'nextDateTime' => $dateTime->add($month) <= $now ? $dateTime->add($month) : null,
        ]);
    }
}
