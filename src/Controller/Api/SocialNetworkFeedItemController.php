<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SocialNetworkFeedItemController extends BaseController
{
    /**
     * Retrieve a list of social network feed items assigned to profiles of a city.
     *
     * You can filter the results by providing optional query parameters.
     *
     * @Operation(
     *     tags={"Social Network Feed Item"},
     *     summary="Retrieve a list of social network feed items assigned to profiles of a city",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier",
     *         in="query",
     *         description="Filter by unique identifier of the feed item",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="networkIdentifier",
     *         in="query",
     *         description="Filter by social network identifier (e.g. twitter, facebook, instagram)",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_citylist', methods: ['GET'])]
    public function listSocialNetworkFeedItemsCityAction(Request $request, City $city): JsonResponse
    {
        $uniqueIdentifier = $request->get('uniqueIdentifier');
        $networkIdentifier = $request->get('networkIdentifier');

        $profileList = $this->managerRegistry->getRepository(SocialNetworkFeedItem::class)->findByCityAndProperties($city, $uniqueIdentifier, $networkIdentifier);

        return $this->createStandardResponse($profileList);
    }

    /**
     * Update properties of a social network feed item.
     *
     * @Operation(
     *     tags={"Social Network Feed Item"},
     *     summary="Update properties of a social network feed item",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="feedItemId",
     *         in="path",
     *         description="Id of the feed item to update",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         description="JSON representation of the feed item properties to update",
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-feeditems/{feedItemId}', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_update', methods: ['POST'])]
    public function updateSocialNetworkFeedItemAction(Request $request, SocialNetworkFeedItem $socialNetworkFeedItem, EntityMergerInterface $entityMerger): JsonResponse
    {
        $updatedSocialNetworkFeedItem = $this->serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $entityMerger->merge($updatedSocialNetworkFeedItem, $socialNetworkFeedItem);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($socialNetworkFeedItem);
    }

    /**
     * Create a new social network feed item and assign it to the provided profile.
     *
     * @Operation(
     *     tags={"Social Network Feed Item"},
     *     summary="Create a new social network feed item",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\RequestBody(
     *         description="JSON representation of the feed item to create",
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returned when successfully created"
     *     ),
     *     @OA\Response(
     *         response="409",
     *         description="Returned when feed item already exists"
     *     )
     * )
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_create', methods: ['PUT'])]
    public function createSocialNetworkFeedItemAction(Request $request): JsonResponse
    {
        $newSocialNetworkFeedItem = $this->serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $newSocialNetworkFeedItem->setCreatedAt(new \DateTime());

        try {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($newSocialNetworkFeedItem);
            $manager->flush();
        } catch (UniqueConstraintViolationException) {
            return $this->createErrors(JsonResponse::HTTP_CONFLICT, ['This feed item already exists.']);
        } catch (\Exception) {
            return $this->createErrors(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, ['An unknown error occured. Please try again later or report this issue to criticalmass@caldera.cc.']);
        }

        return $this->createStandardResponse($newSocialNetworkFeedItem, null, JsonResponse::HTTP_CREATED);
    }
}
