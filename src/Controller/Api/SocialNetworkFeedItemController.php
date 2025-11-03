<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Social Network Feed Item')]
class SocialNetworkFeedItemController extends BaseController
{
    #[Route(
        path: '/api/{citySlug}/socialnetwork-feeditems',
        name: 'caldera_criticalmass_rest_socialnetwork_feeditems_citylist',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{citySlug}/socialnetwork-feeditems',
        summary: 'Returns a list of social network feed items for a specified city.',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                description: 'City slug for the corresponding social network feed items',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'uniqueIdentifier',
                description: 'Only return items matching this unique identifier',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'networkIdentifier',
                description: 'Limit results to the specified social network',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listSocialNetworkFeedItemsCityAction(Request $request, City $city): JsonResponse
    {
        $uniqueIdentifier  = $request->query->get('uniqueIdentifier');
        $networkIdentifier = $request->query->get('networkIdentifier');

        $feedItemList = $this->managerRegistry
            ->getRepository(SocialNetworkFeedItem::class)
            ->findByCityAndProperties($city, $uniqueIdentifier, $networkIdentifier);

        return $this->createStandardResponse($feedItemList);
    }

    #[Route(
        path: '/api/{citySlug}/socialnetwork-feeditems/{feedItemId}',
        name: 'caldera_criticalmass_rest_socialnetwork_feeditems_update',
        methods: ['POST']
    )]
    #[OA\Post(
        path: '/api/{citySlug}/socialnetwork-feeditems/{feedItemId}',
        summary: 'Update properties of a social network feed item.',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                description: 'City slug for the corresponding social network feed item',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'feedItemId',
                description: 'ID of the social network feed item to update',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Serialized Feed Item Content',
            required: true,
            content: new OA\JsonContent(type: 'object')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 404, description: 'Feed item not found'),
        ]
    )]
    public function updateSocialNetworkFeedItemAction(
        Request $request,
        SocialNetworkFeedItem $socialNetworkFeedItem,
        EntityMergerInterface $entityMerger
    ): JsonResponse {
        /** @var SocialNetworkFeedItem $updatedSocialNetworkFeedItem */
        $updatedSocialNetworkFeedItem = $this->serializer->deserialize(
            $request->getContent(),
            SocialNetworkFeedItem::class,
            'json'
        );

        $entityMerger->merge($updatedSocialNetworkFeedItem, $socialNetworkFeedItem);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($socialNetworkFeedItem);
    }

    #[Route(
        path: '/api/{citySlug}/socialnetwork-feeditems',
        name: 'caldera_criticalmass_rest_socialnetwork_feeditems_create',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/api/{citySlug}/socialnetwork-feeditems',
        summary: 'Create a new social network feed item and assign it to the provided profile.',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                description: 'City slug for the corresponding social network feed item',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Serialized Feed Item Content',
            required: true,
            content: new OA\JsonContent(type: 'object')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 409, description: 'Feed item already exists'),
            new OA\Response(response: 500, description: 'Unknown server error'),
        ]
    )]
    public function createSocialNetworkFeedItemAction(Request $request): JsonResponse
    {
        /** @var SocialNetworkFeedItem $newSocialNetworkFeedItem */
        $newSocialNetworkFeedItem = $this->serializer->deserialize(
            $request->getContent(),
            SocialNetworkFeedItem::class,
            'json'
        );

        $newSocialNetworkFeedItem->setCreatedAt(new \DateTime());

        try {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($newSocialNetworkFeedItem);
            $manager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            return $this->createErrors(JsonResponse::HTTP_CONFLICT, ['This feed item already exists.']);
        } catch (\Exception $exception) {
            return $this->createErrors(
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                ['An unknown error occured. Please try again later or report this issue to criticalmass@caldera.cc.']
            );
        }

        return $this->createStandardResponse($newSocialNetworkFeedItem, null, JsonResponse::HTTP_CREATED);
    }
}
