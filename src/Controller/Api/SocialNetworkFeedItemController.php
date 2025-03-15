<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: "Social Network Feed Item")]
class SocialNetworkFeedItemController extends BaseController
{
    /**
     * Returns a list of social network feed items for a specified city.
     *
     * @ParamConverter("city", class="App:City")
     */
    #[OA\Response(
        response: 200,
        description: "Returned when successful"
    )]
    #[OA\Parameter(
        name: 'citySlug',
        description: 'Provide a city slug for the corresponding socialnetwork feed item',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'uniqueIdentifier',
        description: 'Only return items matching this unique identifier',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'networkIdentifier',
        description: 'Limit results to the specified social network',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[Route(path: '/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_citylist', methods: ['GET'])]
    public function listSocialNetworkFeedItemsCityAction(Request $request, City $city): JsonResponse
    {
        $uniqueIdentifier = $request->get('uniqueIdentifier');
        $networkIdentifier = $request->get('networkIdentifier');

        $feedItemList = $this->managerRegistry->getRepository(SocialNetworkFeedItem::class)->findByCityAndProperties($city, $uniqueIdentifier, $networkIdentifier);

        return $this->createStandardResponse($feedItemList);
    }

    /**
     * Update properties of a social network feed item.
     * @ParamConverter("socialNetworkFeedItem", class="App:SocialNetworkFeedItem")
     */
    #[OA\RequestBody(
        description: "Serialized Feed Item Content",
        required: true,
        content: new OA\JsonContent()
    )]
    #[OA\Response(
        response: 200,
        description: "Returned when successful"
    )]
    #[OA\Parameter(
        name: 'citySlug',
        description: 'Provide a city slug for the corresponding socialnetwork feed item',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'feedItemId',
        description: 'ID of socialnetwork feed item to update',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[Route(path: '/{citySlug}/socialnetwork-feeditems/{feedItemId}', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_update', methods: ['POST'])]
    public function updateSocialNetworkFeedItemAction(Request $request, SocialNetworkFeedItem $socialNetworkFeedItem, EntityMergerInterface $entityMerger): JsonResponse
    {
        $updatedSocialNetworkFeedItem = $this->serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $entityMerger->merge($updatedSocialNetworkFeedItem, $socialNetworkFeedItem);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($socialNetworkFeedItem);
    }

    /**
     * Create a new social network feed item and assign it to the provided profile.
     */
    #[OA\RequestBody(
        description: "Serialized Feed Item Content",
        required: true,
        content: new OA\JsonContent()
    )]
    #[OA\Response(
        response: 200,
        description: "Returned when successful"
    )]
    #[OA\Parameter(
        name: 'citySlug',
        description: 'Provide a city slug for the corresponding socialnetwork feed item',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[Route(path: '/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_create', methods: ['PUT'])]
    public function createSocialNetworkFeedItemAction(Request $request): JsonResponse
    {
        $newSocialNetworkFeedItem = $this->serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $newSocialNetworkFeedItem->setCreatedAt(new \DateTime());

        try {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($newSocialNetworkFeedItem);
            $manager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            return $this->createErrors(JsonResponse::HTTP_CONFLICT, ['This feed item already exists.']);
        } catch (\Exception $exception) {
            return $this->createErrors(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, ['An unknown error occured. Please try again later or report this issue to criticalmass@caldera.cc.']);
        }

        return $this->createStandardResponse($newSocialNetworkFeedItem, null, JsonResponse::HTTP_CREATED);
    }
}
