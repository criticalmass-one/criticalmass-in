<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Subride;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubrideController extends BaseController
{
    /**
     * Retrieve a list of subrides of a ride.
     *
     * Subrides are smaller events that happen within the context of a main ride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/subride', name: 'caldera_criticalmass_rest_subride_list', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Subride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listSubrideAction(Ride $ride): JsonResponse
    {
        $subrideList = $this->managerRegistry->getRepository(Subride::class)->findByRide($ride);

        return $this->createStandardResponse($subrideList);
    }

    /**
     * Show details of a specified subride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/{id}', name: 'caldera_criticalmass_rest_subride_show', requirements: ['id' => '\d+'], methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Subride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the subride', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function showSubrideAction(Subride $subride): JsonResponse
    {
        return $this->createStandardResponse($subride);
    }

    /**
     * Creates a new subride for a ride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/subride', name: 'caldera_criticalmass_rest_subride_create', methods: ['PUT'], priority: 190)]
    #[OA\Tag(name: 'Subride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the subride', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 400, description: 'Returned when the submitted subride is invalid')]
    public function createSubrideAction(Request $request, Ride $ride, ValidatorInterface $validator): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['body' => 'A JSON object is required.']);
        }

        if (!isset($payload['dateTime'])) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['dateTime' => 'A dateTime is required.']);
        }

        try {
            $dateTime = new \DateTime((string) $payload['dateTime']);
        } catch (\Exception) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['dateTime' => 'dateTime is not a valid datetime.']);
        }

        $subride = new Subride();
        $subride
            ->setRide($ride)
            ->setTitle((string) ($payload['title'] ?? ''))
            ->setLocation((string) ($payload['location'] ?? ''))
            ->setDateTime($dateTime)
            ->setDescription(isset($payload['description']) ? (string) $payload['description'] : null)
            ->setCreatedAt(new \DateTime());

        if (isset($payload['latitude'])) {
            $subride->setLatitude((float) $payload['latitude']);
        }

        if (isset($payload['longitude'])) {
            $subride->setLongitude((float) $payload['longitude']);
        }

        if (null !== $errorResponse = $this->validateSubride($subride, $validator)) {
            return $errorResponse;
        }

        $manager = $this->managerRegistry->getManager();
        $manager->persist($subride);
        $manager->flush();

        return $this->createStandardResponse($subride, ['groups' => ['subride-list']]);
    }

    /**
     * Updates an existing subride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/{subrideId}', name: 'caldera_criticalmass_rest_subride_update', requirements: ['subrideId' => '\d+'], methods: ['POST'], priority: 190)]
    #[OA\Tag(name: 'Subride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'subrideId', in: 'path', description: 'Id of the subride', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(description: 'JSON representation of the subride fields to update', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 404, description: 'Returned when the subride does not belong to the ride')]
    public function updateSubrideAction(Request $request, Ride $ride, int $subrideId, ValidatorInterface $validator): JsonResponse
    {
        $subride = $this->managerRegistry->getRepository(Subride::class)->find($subrideId);

        if (!$subride || $subride->getRide()->getId() !== $ride->getId()) {
            return new JsonResponse(['error' => 'Subride not found'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['body' => 'A JSON object is required.']);
        }

        if (array_key_exists('title', $payload)) {
            $subride->setTitle((string) $payload['title']);
        }

        if (array_key_exists('location', $payload)) {
            $subride->setLocation((string) $payload['location']);
        }

        if (array_key_exists('description', $payload)) {
            $subride->setDescription(null === $payload['description'] ? null : (string) $payload['description']);
        }

        if (array_key_exists('dateTime', $payload)) {
            try {
                $subride->setDateTime(new \DateTime((string) $payload['dateTime']));
            } catch (\Exception) {
                return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['dateTime' => 'dateTime is not a valid datetime.']);
            }
        }

        if (array_key_exists('latitude', $payload)) {
            $subride->setLatitude((float) $payload['latitude']);
        }

        if (array_key_exists('longitude', $payload)) {
            $subride->setLongitude((float) $payload['longitude']);
        }

        if (null !== $errorResponse = $this->validateSubride($subride, $validator)) {
            return $errorResponse;
        }

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($subride, ['groups' => ['subride-list']]);
    }

    /**
     * Deletes a subride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/{subrideId}', name: 'caldera_criticalmass_rest_subride_delete', requirements: ['subrideId' => '\d+'], methods: ['DELETE'], priority: 190)]
    #[OA\Tag(name: 'Subride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'subrideId', in: 'path', description: 'Id of the subride', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 404, description: 'Returned when the subride does not belong to the ride')]
    public function deleteSubrideAction(Ride $ride, int $subrideId): JsonResponse
    {
        $subride = $this->managerRegistry->getRepository(Subride::class)->find($subrideId);

        if (!$subride || $subride->getRide()->getId() !== $ride->getId()) {
            return new JsonResponse(['error' => 'Subride not found'], Response::HTTP_NOT_FOUND);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->remove($subride);
        $manager->flush();

        return new JsonResponse(['status' => 'ok', 'deletedSubrideId' => $subrideId]);
    }

    private function validateSubride(Subride $subride, ValidatorInterface $validator): ?JsonResponse
    {
        $violations = $validator->validate($subride);

        $errors = [];

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        if (0 < count($errors)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, $errors);
        }

        return null;
    }
}
