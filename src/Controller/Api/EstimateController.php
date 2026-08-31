<?php declare(strict_types=1);

namespace App\Controller\Api;

use Doctrine\Persistence\ManagerRegistry;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestParameterList;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Event\RideEstimate\RideEstimateDeletedEvent;
use App\Event\RideEstimate\RideEstimateUpdatedEvent;
use App\Model\CreateEstimateModel;
use App\Repository\RideEstimateRepository;
use App\Serializer\CriticalSerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EstimateController extends BaseController
{
    public function __construct(
        CriticalSerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly DataQueryManagerInterface $dataQueryManager,
        ManagerRegistry $managerRegistry,
    )
    {
        parent::__construct($managerRegistry, $serializer);
    }

    /**
     * Use this endpoint to add an participant estimate like this:
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
    #[OA\Tag(name: 'Estimate')]
    #[OA\RequestBody(description: 'JSON representation of the estimate data', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
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
     * <code>citySlug</code> and <code>rideIdentifier</code>, you don't even have to provide the coordinates. The
     * followig json shows a valid request to this endpoint:
     *
     * <pre>{
     *   "estimation": 6554
     * }</pre>
     *
     * If you like you can provide details about your app or homepage in the <code>source</code> property or just
     * default to null.
     */
    #[Route(path: '/api/estimate', name: 'caldera_criticalmass_rest_estimate_create', methods: ['POST'], priority: 200)]
    #[OA\Tag(name: 'Estimate')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the ride\'s city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the estimate data', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
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

    /**
     * Lists all participant estimates of a ride, including their ids.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/estimates', name: 'caldera_criticalmass_rest_estimate_list', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Estimate')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the ride\'s city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listRideEstimatesAction(Ride $ride, RideEstimateRepository $repository): JsonResponse
    {
        $estimates = [];

        foreach ($repository->findEstimatesByRide($ride) as $estimate) {
            $estimates[] = [
                'id' => $estimate->getId(),
                'estimation' => $estimate->getEstimatedParticipants(),
                'latitude' => $estimate->getLatitude(),
                'longitude' => $estimate->getLongitude(),
                'dateTime' => $estimate->getDateTime()->format(\DateTimeInterface::ATOM),
                'source' => $estimate->getSource(),
                'user' => $estimate->getUser()?->getUsername(),
            ];
        }

        return new JsonResponse(['estimates' => $estimates]);
    }

    /**
     * Updates an existing participant estimate identified by its id.
     */
    #[Route(path: '/api/estimate/{id}', name: 'caldera_criticalmass_rest_estimate_update', methods: ['POST'], priority: 200, requirements: ['id' => '\d+'])]
    #[OA\Tag(name: 'Estimate')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the estimate to update', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(description: 'JSON representation of the estimate data', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 400, description: 'Returned when the submitted data is invalid')]
    public function updateRideEstimateAction(Request $request, RideEstimate $rideEstimate): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['body' => 'A JSON object is required.']);
        }

        if (array_key_exists('estimation', $payload)) {
            if (!is_numeric($payload['estimation']) || (int) $payload['estimation'] < 0) {
                return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['estimation' => 'estimation must be a non-negative number.']);
            }
            $rideEstimate->setEstimatedParticipants((int) $payload['estimation']);
        }

        if (array_key_exists('latitude', $payload)) {
            $rideEstimate->setLatitude(null === $payload['latitude'] ? null : (float) $payload['latitude']);
        }

        if (array_key_exists('longitude', $payload)) {
            $rideEstimate->setLongitude(null === $payload['longitude'] ? null : (float) $payload['longitude']);
        }

        if (array_key_exists('date_time', $payload)) {
            try {
                $rideEstimate->setDateTime(new \DateTime((string) $payload['date_time']));
            } catch (\Exception) {
                return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['date_time' => 'date_time is not a valid datetime.']);
            }
        }

        if (array_key_exists('source', $payload)) {
            $rideEstimate->setSource(null === $payload['source'] ? null : (string) $payload['source']);
        }

        $this->managerRegistry->getManager()->flush();

        $this->eventDispatcher->dispatch(new RideEstimateUpdatedEvent($rideEstimate), RideEstimateUpdatedEvent::NAME);

        return $this->createStandardResponse($rideEstimate);
    }

    /**
     * Deletes a participant estimate identified by its id.
     */
    #[Route(path: '/api/estimate/{id}', name: 'caldera_criticalmass_rest_estimate_delete', methods: ['DELETE'], priority: 200, requirements: ['id' => '\d+'])]
    #[OA\Tag(name: 'Estimate')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the estimate to delete', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function deleteRideEstimateAction(RideEstimate $rideEstimate): JsonResponse
    {
        $id = $rideEstimate->getId();

        $manager = $this->managerRegistry->getManager();
        $manager->remove($rideEstimate);
        $manager->flush();

        $this->eventDispatcher->dispatch(new RideEstimateDeletedEvent($rideEstimate), RideEstimateDeletedEvent::NAME);

        return new JsonResponse(['status' => 'ok', 'deletedId' => $id]);
    }

    protected function createRideEstimate(CreateEstimateModel $model, ?Ride $ride = null): ?RideEstimate
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
