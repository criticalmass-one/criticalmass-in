<?php declare(strict_types=1);

namespace App\Controller\Statistic;

use App\Controller\AbstractController;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Entity\City;
use App\Entity\Region;
use App\Entity\Ride;
use App\Repository\RegionRepository;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticController extends AbstractController
{
    #[Route(
        '/{citySlug}/statistic',
        name: 'caldera_criticalmass_statistic_city',
        priority: 140
    )]
    public function citystatisticAction(
        SeoPageInterface $seoPage,
        RideRepository $rideRepository,
        City $city
    ): Response {
        $rides = $rideRepository->findRidesForCity($city);

        $seoPage->setDescription(sprintf(
            'Critical-Mass-Statistiken aus %s: Teilnehmer, Fahrtdauer, Fahrtlänge, Touren',
            $city->getCity()
        ));

        return $this->render('Statistic/city_statistic.html.twig', [
            'city' => $city,
            'rides' => $rides,
        ]);
    }

    #[Route(
        '/statistic',
        name: 'caldera_criticalmass_statistic_overview',
        priority: 140
    )]
    public function overviewAction(
        SeoPageInterface $seoPage,
        RideRepository $rideRepository,
        RegionRepository $regionRepository
    ): Response {
        /** @var Region $region */
        $region = $regionRepository->find(3);

        $endDateTime = new \DateTime();
        $twoYearInterval = new \DateInterval('P2Y');

        $startDateTime = new \DateTime();
        $startDateTime->sub($twoYearInterval);

        $rides = $rideRepository->findRidesInRegionInInterval($region, $startDateTime, $endDateTime);

        $citiesWithoutEstimates = $this->findCitiesWithoutParticipationEstimates($rides);
        $rides = $this->filterRideList($rides, $citiesWithoutEstimates);

        $cities = [];
        $rideMonths = [];

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $cities[$ride->getCity()->getSlug()] = $ride->getCity();
            $rideMonths[$ride->getDateTime()->format('Y-m')] = $ride->getDateTime()->format('Y-m');
        }

        rsort($rideMonths);

        $seoPage->setDescription('Critical-Mass-Statistiken: Teilnehmer, Fahrtdauer, Fahrtlänge, Touren');

        return $this->render('Statistic/overview.html.twig', [
            'cities' => $cities,
            'rides' => $rides,
            'rideMonths' => $rideMonths,
        ]);
    }

    protected function findCitiesWithoutParticipationEstimates(array $rides): array
    {
        $cityList = [];

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            if (!$ride->getEstimatedParticipants()) {
                $citySlug = $ride->getCity()->getSlug();

                if (array_key_exists($citySlug, $cityList)) {
                    ++$cityList[$citySlug];
                } else {
                    $cityList[$citySlug] = 1;
                }
            }
        }

        return $cityList;
    }

    protected function filterRideList(array $rides, array $cities): array
    {
        $resultList = [];

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getSlug();

            if (!array_key_exists($citySlug, $cities) || $cities[$citySlug] < 18) {
                $resultList[] = $ride;
            }
        }

        return $resultList;
    }
}
