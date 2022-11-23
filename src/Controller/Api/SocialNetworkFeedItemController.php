<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SocialNetworkFeedItemController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Social Network Feed Item"},
     *     summary="Retrieve a list of social network feed items assigned to profiles of a city",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("city", class="App:City")
     */
    #[Route(path: '/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_citylist', methods: ['GET'])]
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
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("socialNetworkFeedItem", class="App:SocialNetworkFeedItem")
     */
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
     *
     * @Operation(
     *     tags={"Social Network Feed Item"},
     *     summary="Create a new social network feed item",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/{citySlug}/socialnetwork-feeditems', name: 'caldera_criticalmass_rest_socialnetwork_feeditems_create', methods: ['PUT'])]
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
