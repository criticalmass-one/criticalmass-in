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
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * Use this endpoint to add an participant estimate like this:
     *
     * <pre>{
     *   "latitude": 53.549280,
     *   "longitude": 9.979589,
     *   "estimation": 6554,
     *   "dateTime": 1506710306
     * }</pre>
     *
     * The ride will be automatically detected by the combination of provided coordinates and dateTime.
     *
     * If you do not provide <code>dateTime</code> it will use the current time.
     *
     * This endpoint is primarly provided for apps with access to the user's current location. If you know which in
     * which ride the user participates, please use the other endpoint and specify <code>citySlug</code> and
     * <code>rideIdentifier</code>.
     *
     * @Operation(
     *     tags={"Estimate"},
     *     summary="Adds an estimation to statistic",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    public function createEstimateAction(Request $request): Response
    {
        /** @var CreateEstimateModel $estimateModel */
        $estimateModel = $this->deserializeRequest($request, $this->serializer, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        if (!$rideEstimation) {
            throw new BadRequestHttpException();
        }

        $this->registry->getManager()->persist($rideEstimation);
        $this->registry->getManager()->flush();

        $this->eventDispatcher->dispatch(RideEstimateCreatedEvent::NAME, new RideEstimateCreatedEvent($rideEstimation));

        $view = View::create();
        $view
            ->setData($rideEstimation)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_CREATED);

        return $this->handleView($view);
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
     * If you do not provide <code>dateTime</code> it will use the current time. As the target ride is specified by
     * <code>citySlug</code> and <code>rideIdentifier</code>, you don’t even have to provide the coordinates. The
     * followig json shows a valid request to this endpoint:
     *
     * <pre>{
     *   "estimation": 6554
     * }</pre>
     *
     * @Operation(
     *     tags={"Estimate"},
     *     summary="Adds an estimation to statistic",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("ride", class="App:Ride")
     */
    public function createRideEstimateAction(Request $request, Ride $ride): Response
    {
        /** @var CreateEstimateModel $estimateModel */
        $estimateModel = $this->deserializeRequest($request, $this->serializer, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel, $ride);

        if (!$rideEstimation) {
            throw new BadRequestHttpException();
        }

        $this->registry->getManager()->persist($rideEstimation);
        $this->registry->getManager()->flush();

        $this->eventDispatcher->dispatch(RideEstimateCreatedEvent::NAME, new RideEstimateCreatedEvent($rideEstimation));

        $view = View::create();
        $view
            ->setData($rideEstimation)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_CREATED);

        return $this->handleView($view);
    }

    protected function createRideEstimate(CreateEstimateModel $model, Ride $ride = null): ?RideEstimate
    {
        if (!$model->getDateTime()) {
            $model->setDateTime(new \DateTime());
        }

        if (!$ride) {
            $ride = $this->findNearestRide($model);

            if (!$ride) {
                return null;
            }
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
