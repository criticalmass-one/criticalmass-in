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

#[OA\Tag(name: 'Estimate')]
class EstimateController extends BaseController
{
    public function __construct(
        protected readonly SerializerInterface $serializer,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly DataQueryManagerInterface $dataQueryManager,
        protected readonly ManagerRegistry $registry
    ) {}

    /**
     * Autodetect ride by coordinates & timestamp.
     */
    #[Route(
        path: '/estimate',
        name: 'caldera_criticalmass_rest_estimate_create',
        methods: ['POST']
    )]
    #[OA\Post(
        path: '/estimate',
        summary: 'Create a participant estimate (ride autodetected by coords/date)',
        requestBody: new OA\RequestBody(
            description: 'JSON representation of the estimate data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateEstimateModel::class))
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 400, description: 'Invalid payload'),
            new OA\Response(response: 404, description: 'Ride not found for given coords/date'),
        ]
    )]
    public function createEstimateAction(Request $request): JsonResponse
    {
        /** @var CreateEstimateModel $estimateModel */
        $estimateModel = $this->deserializeRequest($request, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        if (!$rideEstimation) {
            throw new BadRequestHttpException();
        }

        $em = $this->registry->getManager();
        $em->persist($rideEstimation);
        $em->flush();

        $this->eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimation), RideEstimateCreatedEvent::NAME);

        return $this->createStandardResponse($rideEstimation);
    }

    /**
     * Add estimate for a specific ride (identified by citySlug & rideIdentifier).
     */
    #[Route(
        path: '/{citySlug}/{rideIdentifier}/estimate',
        name: 'caldera_criticalmass_rest_estimate_create_for_ride',
        methods: ['POST']
    )]
    #[OA\Post(
        path: '/{citySlug}/{rideIdentifier}/estimate',
        summary: 'Create a participant estimate for a specific ride',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                description: 'Slug of the ride’s city.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'rideIdentifier',
                description: 'Identifier of the ride.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'JSON representation of the estimate data',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateEstimateModel::class))
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 400, description: 'Invalid payload'),
            new OA\Response(response: 404, description: 'Ride not found'),
        ]
    )]
    public function createRideEstimateAction(Request $request, Ride $ride): JsonResponse
    {
        /** @var CreateEstimateModel $estimateModel */
        $estimateModel = $this->deserializeRequest($request, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel, $ride);

        if (!$rideEstimation) {
            throw new BadRequestHttpException();
        }

        $em = $this->registry->getManager();
        $em->persist($rideEstimation);
        $em->flush();

        $this->eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimation), RideEstimateCreatedEvent::NAME);

        return $this->createStandardResponse($rideEstimation);
    }

    private function createRideEstimate(CreateEstimateModel $model, Ride $ride = null): ?RideEstimate
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

    private function findNearestRide(CreateEstimateModel $model): ?Ride
    {
        $requestParameterList = new RequestParameterList();
        $requestParameterList
            ->add('centerLatitude', (string) $model->getLatitude())
            ->add('centerLongitude', (string) $model->getLongitude())
            ->add('distanceOrderDirection', 'ASC')
            ->add('year', $model->getDateTime()->format('Y'))
            ->add('month', $model->getDateTime()->format('m'))
            ->add('day', $model->getDateTime()->format('d'))
            ->add('size', (string) 1);

        $rideResultList = $this->dataQueryManager->query($requestParameterList, Ride::class);

        if (1 === count($rideResultList)) {
            return array_pop($rideResultList);
        }

        return null;
    }
}
