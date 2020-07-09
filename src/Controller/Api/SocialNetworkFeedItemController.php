<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
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
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Slug of the requested city"}
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     */
    public function listSocialNetworkFeedItemsCityAction(ManagerRegistry $registry, City $city): Response
    {
        $profileList = $registry->getRepository(SocialNetworkFeedItem::class)->findByCity($city);

        $view = View::create();
        $view
            ->setData($profileList)
            ->setFormat('json')
            ->setStatusCode(200);

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
     * @ParamConverter("socialNetworkProfile", class="App:SocialNetworkFeedItem")
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
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }

    /**
     * Create a new social network profile and assign it to the provided city.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Create a new social network profile",
     *  section="Social Network",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Slug of the corresponding city"},
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     */
    public function createSocialNetworkProfileAction(Request $request, City $city, SerializerInterface $serializer, ManagerRegistry $managerRegistry): Response
    {
        $newSocialNetworkProfile = $serializer->deserialize($request->getContent(), SocialNetworkProfile::class, 'json');

        $newSocialNetworkProfile
            ->setCity($city)
            ->setCreatedAt(new \DateTime());

        $manager = $managerRegistry->getManager();
        $manager->persist($newSocialNetworkProfile);
        $manager->flush();

        $context = new Context();

        $view = View::create();
        $view
            ->setData($newSocialNetworkProfile)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
