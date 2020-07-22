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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkFeedItemController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of social network feed items assigned to profiles of a city",
     *  section="Social Network Feed Item",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Slug of the requested city"},
     *    {"name"="uniqueIdentifier", "dataType"="string", "required"=true, "description"="Unique identifier, normally the ressource identifier of the source item"},
     *    {"name"="networkIdentifier", "dataType"="string", "required"=true, "description"="Network identifier of the social network profile"}
     *  }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  description="Update properties of a social network feed item",
     *  section="Social Network Feed Item",
     *  requirements={
     *    {"name"="citySlug", "dataType"="integer", "required"=true, "description"="Slug of the corresponding city"},
     *    {"name"="feedItemId", "dataType"="integer", "required"=true, "description"="Id of the required social network profile"}
     *  }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  description="Create a new social network feed item",
     *  section="Social Network Feed Item",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Slug of the corresponding city"},
     *  }
     * )
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
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }
}
