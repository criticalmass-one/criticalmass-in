<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\CitySlug;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Model\CreateEstimateModel;
use AppBundle\Traits\RepositoryTrait;
use AppBundle\Traits\UtilTrait;
use AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateService;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\User\UserInterface;

class EstimateController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * You can add an estimation of ride participants like this:
     *
     * <pre>{
     *   "latitude": 53.549280,
     *   "longitude": 9.979589,
     *   "estimation": 6554,
     *   "dateTime": 1506710306
     * }</pre>
     *
     * You can also provide a city instead of coordinates:
     *
     * <pre>{
     *   "citySlug": "hamburg",
     *   "estimation": 6554,
     *   "dateTime": 1506710306
     * }</pre>
     *
     * If you do not provide <code>dateTime</code> it will use the current time.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Adds an estimation to statistic"
     * )
     */
    public function createAction(Request $request, UserInterface $user, Serializer $serializer): Response
    {
        $estimateModel = $this->deserializeRequest($request, $serializer,CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        $this->getManager()->persist($rideEstimation);
        $this->getManager()->flush();

        $this->recalculateEstimates($rideEstimation);

        $view = View::create();
        $view
            ->setData($rideEstimation)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    protected function createRideEstimate(CreateEstimateModel $model): RideEstimate
    {
        if (!$model->getDateTime()) {
            $model->setDateTime(new \DateTime());
        }

        $ride = $this->guessRide($model);

        $estimate = new RideEstimate();

        $estimate
            ->setEstimatedParticipants($model->getEstimation())
            ->setLatitude($model->getLatitude())
            ->setLongitude($model->getLongitude())
            ->setDateTime($model->getDateTime())
            ->setRide($ride);

        return $estimate;
    }

    protected function guessRide(CreateEstimateModel $model): ?Ride
    {
        $ride = null;

        if ($model->getCitySlug()) {
            /** @var CitySlug $citySlug */
            $citySlug = $this->getCitySlugRepository()->findOneBySlug($model->getCitySlug());

            if ($citySlug) {
                $city = $citySlug->getCity();

                if ($city) {
                    $ride = $this->getRideRepository()->findCityRideByDate($city, $model->getDateTime());
                }
            }

            return null;
        } elseif ($model->getLatitude() && $model->getLongitude()) {
            $ride = $this->findNearestRide($model);
        }

        return $ride;
    }

    protected function findNearestRide(CreateEstimateModel $model): ?Ride
    {
        /** @var FinderInterface $finder */
        $finder = $this->container->get('fos_elastica.finder.criticalmass_ride.ride');

        $geoQuery = new \Elastica\Query\GeoDistance('pin', [
            'lat' => $model->getLatitude(),
            'lon' => $model->getLongitude(),
        ],
            '25km'
        );

        $dateTimeQuery = new \Elastica\Query\Term([
            'simpleDate' => $model->getDateTime()->format('Y-m-d')
        ]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($geoQuery)
            ->addMust($dateTimeQuery);

        $query = new \Elastica\Query($boolQuery);

        $query->setSize(1);
        $query->setSort([
            '_geo_distance' => [
                'pin' => [
                    $model->getLatitude(),
                    $model->getLongitude(),
                ],
                'order' => 'asc',
                'unit' => 'km',
            ]
        ]);

        $results = $finder->find($query, 1);

        if (is_array($results)) {
            return array_pop($results);
        }

        return null;
    }

    protected function recalculateEstimates(RideEstimate $rideEstimate): void
    {
        if ($rideEstimate->getRide()) {
            $estimateService = $this->get(RideEstimateService::class);
            $estimateService->calculateEstimates($rideEstimate->getRide());
        }
    }
}
