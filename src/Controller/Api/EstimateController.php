<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Model\CreateEstimateModel;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EstimateController extends BaseController
{
    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /** @var DataQueryManagerInterface $dataQueryManager */
    protected $dataQueryManager;

    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct(SerializerInterface $serializer, EventDispatcherInterface $eventDispatcher, DataQueryManagerInterface $dataQueryManager, ManagerRegistry $registry)
    {
        $this->serializer = $serializer;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataQueryManager = $dataQueryManager;
        $this->registry = $registry;
    }

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
     *  description="Adds an estimation to statistic",
     *  section="Estimate"
     * )
     */
    public function createAction(Request $request): Response
    {
        /** @var CreateEstimateModel $estimateModel */
        $estimateModel = $this->deserializeRequest($request, $this->serializer, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        if (!$rideEstimation) {
            throw $this->createNotFoundException();
        }

        $this->registry->getManager()->persist($rideEstimation);
        $this->registry->getManager()->flush();

        $this->eventDispatcher->dispatch(RideEstimateCreatedEvent::NAME, new RideEstimateCreatedEvent($rideEstimation));

        $view = View::create();
        $view
            ->setData($rideEstimation)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    protected function createRideEstimate(CreateEstimateModel $model): ?RideEstimate
    {
        $ride = $this->findNearestRide($model);

        if (!$ride) {
            return null;
        }

        if (!$model->getDateTime()) {
            $model->setDateTime(new \DateTime());
        }

        $estimate = new RideEstimate();

        $estimate
            ->setEstimatedParticipants($model->getEstimation())
            ->setLatitude($model->getLatitude())
            ->setLongitude($model->getLongitude())
            ->setDateTime($model->getDateTime())
            ->setRide($ride);

        return $estimate;
    }

    protected function findNearestRide(CreateEstimateModel $model): ?Ride
    {
        $requestParameterList = new RequestParameterList();
        $requestParameterList
            ->add('centerLatitude', (string)$model->getLatitude())
            ->add('centerLongitude', (string)$model->getLongitude())
            ->add('distanceOrderDirection', 'ASC')
            ->add('year', $model->getDateTime()->format('Y'))
            ->add('month', $model->getDateTime()->format('m'))
            ->add('day', $model->getDateTime()->format('d'))
            ->add('size', (string)1);

        $rideResultList = $this->dataQueryManager->query($requestParameterList, Ride::class);

        if (1 === count($rideResultList)) {
            return array_pop($rideResultList);
        }

        return null;
    }
}
