<?php declare(strict_types=1);

namespace App\Controller\Statistic;

use App\Controller\AbstractController;
use App\Repository\RideRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MonthlyStatsController extends AbstractController
{
    #[Route(
        '/statistic/{year}/{month}',
        name: 'caldera_criticalmass_statistic_ride_month',
        priority: 140
    )]
    public function listRidesAction(
        RideRepository $rideRepository,
        ?int $year = null,
        ?int $month = null
    ): Response {
        $now = Carbon::now();

        if (!$year || !$month) {
            $dateTime = CarbonImmutable::now();

            $year = (int) $dateTime->format('Y');
            $month = (int) $dateTime->format('m');
        } else {
            $dateTimeSpec = sprintf('%d-%d-01', $year, $month);
            $dateTime = CarbonImmutable::parse($dateTimeSpec);
        }

        $rides = $rideRepository->findEstimatedRides($year, $month);

        return $this->render('Statistic/list_rides.html.twig', [
            'rides' => $rides,
            'dateTime' => $dateTime,
            'previousDateTime' => $dateTime->subMonth(),
            'nextDateTime' => $dateTime->addMonth() <= $now ? $dateTime->addMonth() : null,
        ]);
    }
}
