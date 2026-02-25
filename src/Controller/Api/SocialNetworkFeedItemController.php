<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SocialNetworkFeedItemController extends BaseController
{
    /**
     * Retrieve a list of social network feed items assigned to profiles of a city.
     *
     * You can filter the results by providing optional query parameters.
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_citylist', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Social Network Feed Item')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'uniqueIdentifier', in: 'query', description: 'Filter by unique identifier of the feed item', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'networkIdentifier', in: 'query', description: 'Filter by social network identifier (e.g. twitter, facebook, instagram)', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listSocialNetworkFeedItemsCityAction(Request $request, City $city): JsonResponse
    {
        $uniqueIdentifier = $request->get('uniqueIdentifier');
        $networkIdentifier = $request->get('networkIdentifier');

        $profileList = $this->managerRegistry->getRepository(SocialNetworkFeedItem::class)->findByCityAndProperties($city, $uniqueIdentifier, $networkIdentifier);

        return $this->createStandardResponse($profileList);
    }

    /**
     * Update properties of a social network feed item.
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-feeditems/{feedItemId}', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_update', methods: ['POST'], priority: 190)]
    #[OA\Tag(name: 'Social Network Feed Item')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'feedItemId', in: 'path', description: 'Id of the feed item to update', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(description: 'JSON representation of the feed item properties to update', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function updateSocialNetworkFeedItemAction(Request $request, SocialNetworkFeedItem $socialNetworkFeedItem): JsonResponse
    {
        $this->deserializeRequestInto($request, $socialNetworkFeedItem);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($socialNetworkFeedItem);
    }

    /**
     * Create a new social network feed item and assign it to the provided profile.
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_create', methods: ['PUT'], priority: 190)]
    #[OA\Tag(name: 'Social Network Feed Item')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the feed item to create', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 201, description: 'Returned when successfully created')]
    #[OA\Response(response: 409, description: 'Returned when feed item already exists')]
    public function createSocialNetworkFeedItemAction(Request $request): JsonResponse
    {
        $newSocialNetworkFeedItem = $this->deserializeRequest($request, SocialNetworkFeedItem::class);

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

        return $this->createStandardResponse($newSocialNetworkFeedItem, [], JsonResponse::HTTP_CREATED);
    }
}
