<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Post;
use App\Repository\RideRepository;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostController extends BaseController
{
    /**
     * Get a list of posts.
     *
     * You may specify your query with the following parameters.
     *
     * <strong>List length</strong>
     *
     * The length of your results defaults to 10. Use <code>size</code> to request more or less results.
     *
     * <strong>Regional query parameters</strong>
     *
     * <ul>
     * <li><code>citySlug</code>: Limit the resulting list to a city like <code>hamburg</code>, <code>new-york</code> or <code>muenchen</code>.</li>
     * <li><code>rideIdentifier</code>: Reduce the result list for posts related to this specified ride. Must be combined with <code>citySlug</code>.</li>
     * </ul>
     *
     * <strong>Date-related query parameters</strong>
     *
     * <ul>
     * <li><code>year</code>: Retrieve only posts created in the provided <code>year</code>.</li>
     * <li><code>month</code>: Retrieve only posts of the provided <code>year</code> and <code>month</code>. This will only work in combination with the previous <code>year</code> parameter.</li>
     * <li><code>day</code>: Limit the result list to a <code>day</code>. This parameter must be used with <code>year</code> and <code>month</code>.</li>
     * </ul>
     *
     * <strong>Geo query parameters</strong>
     *
     * <ul>
     * <li>Radius query: Specify <code>centerLatitude</code>, <code>centerLongitude</code> and a <code>radius</code> to retrieve all results within this circle.</li>
     * <li>Bounding Box query: Fetch all posts in the box described by <code>bbNorthLatitude</code>, <code>bbEastLongitude</code> and <code>bbSouthLatitude</code>, <code>bbWestLongitude</code>.
     * </ul>
     *
     * <strong>Order parameters</strong>
     *
     * Sort the resulting list with the parameter <code>orderBy</code> and choose from one of the following properties:
     *
     * <ul>
     * <li><code>id</code></li>
     * <li><code>dateTime</code></li>
     * <li><code>latitude</code></li>
     * <li><code>longitude</code></li>
     * </ul>
     *
     * Specify the order direction with <code>orderDirection=asc</code> or <code>orderDirection=desc</code>.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     */
    #[Route(path: '/api/post', name: 'caldera_criticalmass_rest_post_list', methods: ['GET'], priority: 200)]
    #[OA\Tag(name: 'Post')]
    #[OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'query', description: 'Provide a ride identifier', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'year', in: 'query', description: 'Limit the result set to this year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'month', in: 'query', description: 'Limit the result set to this month. Must be combined with year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'day', in: 'query', description: 'Limit the result set to this day.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'centerLatitude', in: 'query', description: 'Latitude of a coordinate to search posts around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'centerLongitude', in: 'query', description: 'Longitude of a coordinate to search posts around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'radius', in: 'query', description: 'Radius to look around for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbEastLongitude', in: 'query', description: 'East longitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbWestLongitude', in: 'query', description: 'West longitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbNorthLatitude', in: 'query', description: 'North latitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbSouthLatitude', in: 'query', description: 'South latitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'orderBy', in: 'query', description: 'Choose a property to sort the list by.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'orderDirection', in: 'query', description: 'Sort ascending or descending.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'startValue', in: 'query', description: 'Start ordered list with provided value.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list. Defaults to 10.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);

        $postList = $dataQueryManager->query($queryParameterList, Post::class);

        return $this->createStandardResponse($postList, ['groups' => ['post-list']]);
    }

    /**
     * Show a single post.
     */
    #[Route(path: '/api/post/{id}', name: 'caldera_criticalmass_rest_post_show', requirements: ['id' => '\d+'], methods: ['GET'], priority: 200)]
    #[OA\Tag(name: 'Post')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the post', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function showAction(Post $post): JsonResponse
    {
        return $this->createStandardResponse($post, ['groups' => ['post-list']]);
    }

    /**
     * Creates a new post for a city, optionally attached to a ride.
     */
    #[Route(path: '/api/{citySlug}/post', name: 'caldera_criticalmass_rest_post_create', methods: ['PUT'], priority: 190)]
    #[OA\Tag(name: 'Post')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the post (message required, optional rideIdentifier)', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 400, description: 'Returned when the submitted post is invalid')]
    public function createPostAction(Request $request, City $city, RideRepository $rideRepository, ValidatorInterface $validator): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['body' => 'A JSON object is required.']);
        }

        try {
            $dateTime = isset($payload['dateTime']) ? new \DateTime((string) $payload['dateTime']) : new \DateTime();
        } catch (\Exception) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['dateTime' => 'dateTime is not a valid datetime.']);
        }

        $post = new Post();
        $post
            ->setCity($city)
            ->setMessage((string) ($payload['message'] ?? ''))
            ->setDateTime($dateTime)
            ->setEnabled(true);

        if (isset($payload['rideIdentifier']) && '' !== trim((string) $payload['rideIdentifier'])) {
            $rideIdentifier = (string) $payload['rideIdentifier'];

            try {
                $ride = $rideRepository->findByCitySlugAndRideDate($city->getMainSlugString(), $rideIdentifier);
            } catch (\Exception) {
                $ride = null;
            }

            $ride ??= $rideRepository->findOneByCitySlugAndSlug($city->getMainSlugString(), $rideIdentifier);

            if (null === $ride) {
                return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['rideIdentifier' => 'Ride not found.']);
            }

            $post->setRide($ride);
        }

        if (isset($payload['latitude'])) {
            $post->setLatitude((float) $payload['latitude']);
        }

        if (isset($payload['longitude'])) {
            $post->setLongitude((float) $payload['longitude']);
        }

        if (null !== $errorResponse = $this->validatePost($post, $validator)) {
            return $errorResponse;
        }

        $manager = $this->managerRegistry->getManager();
        $manager->persist($post);
        $manager->flush();

        return $this->createStandardResponse($post, ['groups' => ['post-list']]);
    }

    /**
     * Updates an existing post.
     */
    #[Route(path: '/api/post/{id}', name: 'caldera_criticalmass_rest_post_update', requirements: ['id' => '\d+'], methods: ['POST'], priority: 200)]
    #[OA\Tag(name: 'Post')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the post', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(description: 'JSON representation of the post fields to update', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 400, description: 'Returned when the submitted post is invalid')]
    public function updatePostAction(Request $request, Post $post, ValidatorInterface $validator): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['body' => 'A JSON object is required.']);
        }

        if (array_key_exists('message', $payload)) {
            $post->setMessage((string) $payload['message']);
        }

        if (array_key_exists('enabled', $payload)) {
            if (!is_bool($payload['enabled'])) {
                return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['enabled' => 'enabled must be a boolean.']);
            }
            $post->setEnabled($payload['enabled']);
        }

        if (array_key_exists('latitude', $payload)) {
            $post->setLatitude((float) $payload['latitude']);
        }

        if (array_key_exists('longitude', $payload)) {
            $post->setLongitude((float) $payload['longitude']);
        }

        if (array_key_exists('dateTime', $payload)) {
            try {
                $post->setDateTime(new \DateTime((string) $payload['dateTime']));
            } catch (\Exception) {
                return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, ['dateTime' => 'dateTime is not a valid datetime.']);
            }
        }

        if (null !== $errorResponse = $this->validatePost($post, $validator)) {
            return $errorResponse;
        }

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($post, ['groups' => ['post-list']]);
    }

    /**
     * Deletes a post. Replies (child posts) are detached and kept.
     */
    #[Route(path: '/api/post/{id}', name: 'caldera_criticalmass_rest_post_delete', requirements: ['id' => '\d+'], methods: ['DELETE'], priority: 200)]
    #[OA\Tag(name: 'Post')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the post', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function deletePostAction(Post $post): JsonResponse
    {
        $id = $post->getId();
        $manager = $this->managerRegistry->getManager();

        $children = $manager->getRepository(Post::class)->findBy(['parent' => $post]);
        foreach ($children as $child) {
            $child->setParent(null);
        }
        $manager->flush();

        $manager->remove($post);
        $manager->flush();

        return new JsonResponse(['status' => 'ok', 'deletedPostId' => $id]);
    }

    private function validatePost(Post $post, ValidatorInterface $validator): ?JsonResponse
    {
        $violations = $validator->validate($post);

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
