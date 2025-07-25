<?php declare(strict_types=1);

namespace App\Controller\Api;

use Doctrine\Persistence\ManagerRegistry;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestParameterList;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Model\CreateEstimateModel;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[OA\Tag(name: "Estimate")]
class EstimateController extends BaseController
{
    public function __construct(
        protected readonly SerializerInterface $serializer,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly DataQueryManagerInterface $dataQueryManager,
        protected readonly ManagerRegistry $registry
    ) {

    }

    /**
     * Use this endpoint to add a participant estimate like this:
     *
     * <pre>{
     *   "latitude": 53.549280,
     *   "longitude": 9.979589,
     *   "estimation": 6554,
     *   "date_time": 1506710306,
     *   "source": "your website or app homepage here?"
     * }</pre>
     *
     * The ride will be automatically detected by the combination of provided coordinates and dateTime.
     *
     * If you do not provide <code>date_time</code> it will use the current time.
     *
     * This endpoint is primarly provided for apps with access to the user's current location. If you like you can
     * provide details about your app or homepage in the <code>source</code> property or just default to null.

     * If you know which in which ride the user participates, please use the other endpoint and specify
     * <code>citySlug</code> and <code>rideIdentifier</code>.
     */
    #[OA\RequestBody(
        description: "JSON representation of the estimate data",
        required: true,
        content: new OA\JsonContent(ref: new Model(type: RideEstimate::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Returned when successful."
    )]
    public function createEstimateAction(Request $request): JsonResponse
    {
        /** @var CreateEstimateModel $estimateModel */
        $estimateModel = $this->deserializeRequest($request, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        if (!$rideEstimation) {
            throw new BadRequestHttpException();
        }

        $this->managerRegistry->getManager()->persist($rideEstimation);
        $this->managerRegistry->getManager()->flush();

        $this->eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimation), RideEstimateCreatedEvent::NAME);

        return $this->createStandardResponse($rideEstimation);
    }

    /**
     * You can add an estimation of ride participants like this:
     *
     * <pre>{
     *   "latitude": 53.549280,
     *   "longitude": 9.979589,
     *   "estimation": 6554,
     *   "date_time": 1506710306,
     *   "source": "your website or app homepage here?"
     * }</pre>
     *
     * If you do not provide <code>date_time</code> it will use the current time. As the target ride is specified by
     * <code>citySlug</code> and <code>rideIdentifier</code>, you don’t even have to provide the coordinates. The
     * followig json shows a valid request to this endpoint:
     *
     * <pre>{
     *   "estimation": 6554
     * }</pre>
     *
     * If you like you can provide details about your app or homepage in the <code>source</code> property or just
     * default to null.
     *
     */
    #[OA\Parameter(
        name: "citySlug",
        description: "Slug of the ride’s city.",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "rideIdentifier",
        description: "Identifier of the ride.",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\RequestBody(
        description: "JSON representation of the estimate data",
        required: true,
        content: new OA\JsonContent(ref: new Model(type: RideEstimate::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Returned when successful."
    )]
    #[Route(
        path: "/estimate",
        name: "caldera_criticalmass_rest_estimate_create",
        methods: ["POST"]
    )]
    public function createRideEstimateAction(Request $request, Ride $ride): JsonResponse
    {
        /** @var CreateEstimateModel $estimateModel */
        $estimateModel = $this->deserializeRequest($request, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel, $ride);

        if (!$rideEstimation) {
            throw new BadRequestHttpException();
        }

        $this->managerRegistry->getManager()->persist($rideEstimation);
        $this->managerRegistry->getManager()->flush();

        $this->eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimation), RideEstimateCreatedEvent::NAME);

        return $this->createStandardResponse($rideEstimation);
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
            ->setSource($model->getSource())
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
