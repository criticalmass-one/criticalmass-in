<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
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
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/socialnetwork-feeditems", name="caldera_criticalmass_rest_socialnetwork_feeditems_citylist", methods={"GET"})
     */
    public function listSocialNetworkFeedItemsCityAction(Request $request, ManagerRegistry $registry, SerializerInterface $serializer, City $city): JsonResponse
    {
        $uniqueIdentifier = $request->get('uniqueIdentifier');
        $networkIdentifier = $request->get('networkIdentifier');

        $profileList = $registry->getRepository(SocialNetworkFeedItem::class)->findByCityAndProperties($city, $uniqueIdentifier, $networkIdentifier);

        return new JsonResponse($serializer->serialize($profileList, 'json'), JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Update properties of a social network feed item.
     *
     * @Operation(
     *     tags={"Social Network Feed Item"},
     *     summary="Update properties of a social network feed item",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("socialNetworkFeedItem", class="App:SocialNetworkFeedItem")
     * @Route("/{citySlug}/socialnetwork-feeditems/{feedItemId}", name="caldera_criticalmass_rest_socialnetwork_feeditems_update", methods={"POST"})
     */
    public function updateSocialNetworkFeedItemAction(Request $request, SocialNetworkFeedItem $socialNetworkFeedItem, SerializerInterface $serializer, ManagerRegistry $managerRegistry, EntityMergerInterface $entityMerger): JsonResponse
    {
        $updatedSocialNetworkFeedItem = $serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $entityMerger->merge($updatedSocialNetworkFeedItem, $socialNetworkFeedItem);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($socialNetworkFeedItem, 'json'), JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Create a new social network feed item and assign it to the provided profile.
     *
     * @Operation(
     *     tags={"Social Network Feed Item"},
     *     summary="Create a new social network feed item",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @Route("/{citySlug}/socialnetwork-feeditems", name="caldera_criticalmass_rest_socialnetwork_feeditems_create", methods={"PUT"})
     */
    public function createSocialNetworkFeedItemAction(Request $request, SerializerInterface $serializer, ManagerRegistry $managerRegistry): JsonResponse
    {
        $newSocialNetworkFeedItem = $serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $newSocialNetworkFeedItem->setCreatedAt(new \DateTime());

        try {
            $manager = $managerRegistry->getManager();
            $manager->persist($newSocialNetworkFeedItem);
            $manager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            return $this->createError(JsonResponse::HTTP_CONFLICT, 'This feed item already exists.');
        } catch (\Exception $exception) {
            return $this->createError(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'An unknown error occured. Please try again later or report this issue to criticalmass@caldera.cc.');
        }

        return new JsonResponse($serializer->serialize($newSocialNetworkFeedItem, 'json'), JsonResponse::HTTP_CREATED, [], true);
    }
}
