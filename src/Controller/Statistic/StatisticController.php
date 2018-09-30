<?php declare(strict_types=1);

namespace App\Controller\Statistic;

use App\Controller\AbstractController;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Entity\City;
use App\Entity\Region;
use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class StatisticController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function citystatisticAction(SeoPageInterface $seoPage, City $city): Response
    {
        $rides = $this->getRideRepository()->findRidesForCity($city);

        $seoPage->setDescription(sprintf('Critical-Mass-Statistiken aus %s: Teilnehmer, Fahrtdauer, Fahrtlänge, Touren', $city->getCity()));

        return $this->render('Statistic/city_statistic.html.twig', [
            'city' => $city,
            'rides' => $rides,
        ]);
    }

    public function overviewAction(SeoPageInterface $seoPage): Response
    {
        /** @var Region $region */
        $region = $this->getRegionRepository()->find(3);

        $endDateTime = new \DateTime();
        $twoYearInterval = new \DateInterval('P2Y');

        $startDateTime = new \DateTime();
        $startDateTime->sub($twoYearInterval);

        $rides = $this->getRideRepository()->findRidesInRegionInInterval($region, $startDateTime, $endDateTime);

        $citiesWithoutEstimates = $this->findCitiesWithoutParticipationEstimates($rides);
        $rides = $this->filterRideList($rides, $citiesWithoutEstimates);

        $cities = [];

        $rideMonths = [];

        /**
         * @var Ride $ride
         */
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

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getSlug();

            if (!array_key_exists($citySlug, $cities) || $cities[$citySlug] < 18) {
                $resultList[] = $ride;
            }
        }

        return $resultList;
    }
}
