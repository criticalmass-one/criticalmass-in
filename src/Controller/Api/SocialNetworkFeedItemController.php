<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     */
    public function listSocialNetworkFeedItemsCityAction(Request $request, ManagerRegistry $registry, City $city): Response
    {
        $uniqueIdentifier = $request->get('uniqueIdentifier');
        $networkIdentifier = $request->get('networkIdentifier');

        $profileList = $registry->getRepository(SocialNetworkFeedItem::class)->findByCityAndProperties($city, $uniqueIdentifier, $networkIdentifier);

        $view = View::create();
        $view
            ->setData($profileList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
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
     */
    public function updateSocialNetworkFeedItemAction(Request $request, SocialNetworkFeedItem $socialNetworkFeedItem, SerializerInterface $serializer, ManagerRegistry $managerRegistry, EntityMergerInterface $entityMerger): Response
    {
        $updatedSocialNetworkFeedItem = $serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $entityMerger->merge($updatedSocialNetworkFeedItem, $socialNetworkFeedItem);

        $managerRegistry->getManager()->flush();

        $context = new Context();

        $view = View::create();
        $view
            ->setData($socialNetworkFeedItem)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK)
            ->setContext($context);

        return $this->handleView($view);
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
     *
     */
    public function createSocialNetworkFeedItemAction(Request $request, SerializerInterface $serializer, ManagerRegistry $managerRegistry): Response
    {
        $newSocialNetworkFeedItem = $serializer->deserialize($request->getContent(), SocialNetworkFeedItem::class, 'json');

        $newSocialNetworkFeedItem->setCreatedAt(new \DateTime());

        $context = new Context();

        $view = View::create();
        $view
            ->setFormat('json')
            ->setContext($context);

        try {
            $manager = $managerRegistry->getManager();
            $manager->persist($newSocialNetworkFeedItem);
            $manager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            return $this->createError(Response::HTTP_CONFLICT, 'This feed item already exists.');
        } catch (\Exception $exception) {
            return $this->createError(Response::HTTP_INTERNAL_SERVER_ERROR, 'An unknown error occured. Please try again later or report this issue to criticalmass@caldera.cc.');
        }

        $view
            ->setData($newSocialNetworkFeedItem)
            ->setStatusCode(Response::HTTP_CREATED);

        return $this->handleView($view);
    }
}
