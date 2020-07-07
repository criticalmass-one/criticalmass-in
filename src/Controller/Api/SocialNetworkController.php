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

class SocialNetworkController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of social network profiles assigned to a city",
     *  section="Social Network",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Retrieve a list of social network profiles assigned to a city"}
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     */
    public function listSocialNetworkProfilesAction(ManagerRegistry $registry, City $city): Response
    {
        $profileList = $registry->getRepository(SocialNetworkProfile::class)->findByCity($city);

        $view = View::create();
        $view
            ->setData($profileList)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Update properties of a social network profile.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update properties of a social network profile",
     *  section="Social Network",
     *  requirements={
     *    {"name"="profileId", "dataType"="integer", "required"=true, "description"="Id of the required social network profile."},
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
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
