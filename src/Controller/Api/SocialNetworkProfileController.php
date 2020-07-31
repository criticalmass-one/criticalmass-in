<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkProfileController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Search for social network profiles",
     *  section="Social Network Profile",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=false, "description"="Reduce the list to profiles of the provided city"},
     *    {"name"="autoFetch", "dataType"="boolean", "required"=false, "description"="Set true to get only auto fetchable profiles"},
     *    {"name"="networkIdentifier", "dataType"="string", "required"=false, "description"="Identifier of the social network type"},
     *    {"name"="entities", "dataType"="string", "required"=false, "description"="Limit the result to those specified entity type"}
     *  }
     * )
     * @ParamConverter("city", class="App:City", isOptional="true")
     */
    public function listSocialNetworkProfilesAction(Request $request, ManagerRegistry $registry, City $city = null, SerializerInterface $serializer): Response
    {
        $networkIdentifier = $request->get('networkIdentifier');
        $autoFetch = (bool)$request->get('autoFetch');

        if ($entities = $request->get('entities')) {
            $entityClassNames = explode(',', $entities);
        } else {
            $entityClassNames = [];
        }

        $profileList = $registry->getRepository(SocialNetworkProfile::class)->findByProperties($networkIdentifier, $autoFetch, $city, $entityClassNames);

        $view = View::create();
        $view
            ->setData($profileList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of social network profiles assigned to a city",
     *  section="Social Network Profile",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Retrieve a list of social network profiles assigned to a city"}
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     */
    public function listSocialNetworkProfilesCityAction(ManagerRegistry $registry, City $city): Response
    {
        $profileList = $registry->getRepository(SocialNetworkProfile::class)->findByCity($city);

        $view = View::create();
        $view
            ->setData($profileList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Update properties of a social network profile.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update properties of a social network profile",
     *  section="Social Network Profile",
     *  requirements={
     *    {"name"="profileId", "dataType"="integer", "required"=true, "description"="Id of the required social network profile"},
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Slug of the corresponding city"},
     *  }
     * )
     * @ParamConverter("socialNetworkProfile", class="App:SocialNetworkProfile")
     */
    public function updateSocialNetworkProfileAction(Request $request, SocialNetworkProfile $socialNetworkProfile, SerializerInterface $serializer, ManagerRegistry $managerRegistry, EntityMergerInterface $entityMerger): Response
    {
        $updatedSocialNetworkProfile = $serializer->deserialize($request->getContent(), SocialNetworkProfile::class, 'json');

        $entityMerger->merge($updatedSocialNetworkProfile, $socialNetworkProfile);

        $managerRegistry->getManager()->flush();

        $context = new Context();

        $view = View::create();
        $view
            ->setData($socialNetworkProfile)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK)
            ->setContext($context);

        return $this->handleView($view);
    }

    /**
     * Create a new social network profile and assign it to the provided city.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Create a new social network profile",
     *  section="Social Network Profile",
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
            ->setStatusCode(Response::HTTP_CREATED)
            ->setContext($context);

        return $this->handleView($view);
    }
}
