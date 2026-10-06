<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use App\Repository\LocationRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class LocationController extends BaseController
{
    /**
     * Retrieve a list of locations of a city.
     */
    #[Route(path: '/api/{citySlug}/location', name: 'caldera_criticalmass_rest_location_list', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Location')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listLocationAction(City $city): JsonResponse
    {
        $locationList = $this->managerRegistry->getRepository(Location::class)->findLocationsByCity($city);

        return $this->createStandardResponse($locationList);
    }

    /**
     * Show details of a specified location.
     */
    #[Route(path: '/api/{citySlug}/location/{slug}', name: 'caldera_criticalmass_rest_location_show', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Location')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'locationSlug', in: 'path', description: 'Slug of the location', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function showLocationAction(Location $location): JsonResponse
    {
        return $this->createStandardResponse($location);
    }

    /**
     * Creates a new location for a city.
     */
    #[Route(path: '/api/{citySlug}/location', name: 'caldera_criticalmass_rest_location_create', methods: ['PUT'], priority: 190)]
    #[OA\Tag(name: 'Location')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the location', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 400, description: 'Returned when the title is missing')]
    #[OA\Response(response: 409, description: 'Returned when a location with this slug already exists in the city')]
    public function createLocationAction(Request $request, City $city, SluggerInterface $slugger, LocationRepository $locationRepository): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['body' => 'A JSON object is required.']);
        }

        $title = trim((string) ($payload['title'] ?? ''));

        if ('' === $title) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['title' => 'A title is required.']);
        }

        $slug = trim((string) ($payload['slug'] ?? ''));
        if ('' === $slug) {
            $slug = strtolower((string) $slugger->slug($title));
        }

        if (null !== $locationRepository->findOneBy(['city' => $city, 'slug' => $slug])) {
            return $this->createErrors(JsonResponse::HTTP_CONFLICT, ['slug' => sprintf('A location with slug "%s" already exists in this city.', $slug)]);
        }

        $location = new Location();
        $location
            ->setCity($city)
            ->setTitle($title)
            ->setSlug($slug)
            ->setDescription(isset($payload['description']) ? (string) $payload['description'] : null)
            ->setLatitude(isset($payload['latitude']) ? (float) $payload['latitude'] : null)
            ->setLongitude(isset($payload['longitude']) ? (float) $payload['longitude'] : null);

        $manager = $this->managerRegistry->getManager();
        $manager->persist($location);
        $manager->flush();

        return $this->createStandardResponse($location);
    }

    /**
     * Updates an existing location.
     */
    #[Route(path: '/api/{citySlug}/location/{slug}', name: 'caldera_criticalmass_rest_location_update', methods: ['POST'], priority: 190)]
    #[OA\Tag(name: 'Location')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'slug', in: 'path', description: 'Slug of the location', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the location fields to update', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 404, description: 'Returned when the location does not exist in the city')]
    public function updateLocationAction(Request $request, City $city, string $slug, LocationRepository $locationRepository): JsonResponse
    {
        $location = $locationRepository->findOneBy(['city' => $city, 'slug' => $slug]);

        if (!$location) {
            return new JsonResponse(['error' => 'Location not found'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['body' => 'A JSON object is required.']);
        }

        if (array_key_exists('title', $payload)) {
            $title = trim((string) $payload['title']);
            if ('' === $title) {
                return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['title' => 'The title must not be empty.']);
            }
            $location->setTitle($title);
        }

        if (array_key_exists('description', $payload)) {
            $location->setDescription(null === $payload['description'] ? null : (string) $payload['description']);
        }

        if (array_key_exists('latitude', $payload)) {
            $location->setLatitude(null === $payload['latitude'] ? null : (float) $payload['latitude']);
        }

        if (array_key_exists('longitude', $payload)) {
            $location->setLongitude(null === $payload['longitude'] ? null : (float) $payload['longitude']);
        }

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($location);
    }

    /**
     * Deletes a location.
     */
    #[Route(path: '/api/{citySlug}/location/{slug}', name: 'caldera_criticalmass_rest_location_delete', methods: ['DELETE'], priority: 190)]
    #[OA\Tag(name: 'Location')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'slug', in: 'path', description: 'Slug of the location', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 404, description: 'Returned when the location does not exist in the city')]
    public function deleteLocationAction(City $city, string $slug, LocationRepository $locationRepository): JsonResponse
    {
        $location = $locationRepository->findOneBy(['city' => $city, 'slug' => $slug]);

        if (!$location) {
            return new JsonResponse(['error' => 'Location not found'], Response::HTTP_NOT_FOUND);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->remove($location);
        $manager->flush();

        return new JsonResponse(['status' => 'ok', 'deletedLocationSlug' => $slug]);
    }
}
