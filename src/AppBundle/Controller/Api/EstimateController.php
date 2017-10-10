<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Model\CreateEstimateModel;
use AppBundle\Statistic\RideEstimate\RideEstimateService;
use AppBundle\Traits\RepositoryTrait;
use AppBundle\Traits\UtilTrait;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\User\UserInterface;

class EstimateController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method"
     * )
     */
    public function createAction(Request $request, UserInterface $user): Response
    {
        $estimateModel = $this->deserializeRequest($request, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        $this->getManager()->persist($rideEstimation);
        $this->getManager()->flush();

        $this->recalculateEstimates($rideEstimation);

        $view = View::create();
        $view
            ->setData($rideEstimation)
            ->setFormat('json')
            ->setStatusCode(200)
        ;

        return $this->handleView($view);
    }

    protected function createRideEstimate(CreateEstimateModel $model): RideEstimate
    {
        $ride = $this->findNearestRide($model);

        $estimate = new RideEstimate();

        $estimate
            ->setEstimatedParticipants($model->getEstimation())
            ->setLatitude($model->getLatitude())
            ->setLongitude($model->getLongitude())
            ->setRide($ride)
        ;

        if ($model->getDateTime()) {
            $estimate
                ->setDateTime($model->getDateTime())
            ;
        }

        return $estimate;
    }

    protected function findNearestRide(CreateEstimateModel $model): ?Ride
    {
        /** @var FinderInterface $finder */
        $finder = $this->container->get('fos_elastica.finder.criticalmass.ride');

        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $model->getLatitude(),
                'lon' => $model->getLongitude()
            ],
            '25km'
        );

        $dateTimeFilter = new \Elastica\Filter\Term(['simpleDate' => $model->getDateTime()->format('Y-m-d')]);

        $boolFilter = new \Elastica\Filter\BoolAnd([$geoFilter, $dateTimeFilter]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $boolFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(1);
        $query->setSort(
            [
                '_geo_distance' =>
                    [
                        'pin' =>
                            [
                                $model->getLatitude(),
                                $model->getLongitude()
                            ],
                        'order' => 'asc',
                        'unit' => 'km'
                    ]
            ]
        );

        $results = $finder->find($query, 1);

        if (is_array($results)) {
            return array_pop($results);
        }

        return null;
    }

    protected function recalculateEstimates(RideEstimate $rideEstimate): void
    {
        if ($rideEstimate->getRide()) {
            /**
             * @var RideEstimateService $estimateService
             */
            $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate');
            $estimateService->calculateEstimates($rideEstimate->getRide());
        }
    }
}
