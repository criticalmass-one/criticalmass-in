<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialNetworkProfileController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Search for social network profiles",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City", isOptional="true")
     * @Route("/socialnetwork-profiles", name="caldera_criticalmass_rest_socialnetwork_profiles_list", methods={"GET"})
     */
    public function listSocialNetworkProfilesAction(Request $request, ManagerRegistry $registry, City $city = null, SerializerInterface $serializer): JsonResponse
    {
        $networkIdentifier = $request->get('networkIdentifier');
        $autoFetch = (bool)$request->get('autoFetch');

        if ($entities = $request->get('entities')) {
            $entityClassNames = explode(',', $entities);
        } else {
            $entityClassNames = [];
        }

        $profileList = $registry->getRepository(SocialNetworkProfile::class)->findByProperties($networkIdentifier, $autoFetch, $city, $entityClassNames);

        return new JsonResponse($serializer->serialize($profileList, 'json'), JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Retrieve a list of social network profiles assigned to a city",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/socialnetwork-profiles", name="caldera_criticalmass_rest_socialnetwork_profiles_citylist", methods={"GET"})
     */
    public function listSocialNetworkProfilesCityAction(ManagerRegistry $registry, SerializerInterface $serializer, City $city): JsonResponse
    {
        $profileList = $registry->getRepository(SocialNetworkProfile::class)->findByCity($city);

        return new JsonResponse($serializer->serialize($profileList, 'json'), JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Update properties of a social network profile.
     *
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Update properties of a social network profile",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("socialNetworkProfile", class="App:SocialNetworkProfile")
     * @Route("/{citySlug}/socialnetwork-profiles/{profileId}", name="caldera_criticalmass_rest_socialnetwork_profiles_update", methods={"POST"})
     */
    public function updateSocialNetworkProfileAction(Request $request, SocialNetworkProfile $socialNetworkProfile, SerializerInterface $serializer, ManagerRegistry $managerRegistry, EntityMergerInterface $entityMerger): Response
    {
        $updatedSocialNetworkProfile = $serializer->deserialize($request->getContent(), SocialNetworkProfile::class, 'json');

        $entityMerger->merge($updatedSocialNetworkProfile, $socialNetworkProfile);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($socialNetworkProfile, 'json',), JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Create a new social network profile and assign it to the provided city.
     *
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Create a new social network profile",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/socialnetwork-profiles", name="caldera_criticalmass_rest_socialnetwork_profiles_create", methods={"PUT"})
     */
    public function createSocialNetworkProfileAction(Request $request, City $city, SerializerInterface $serializer, ManagerRegistry $managerRegistry): JsonResponse
    {
        $newSocialNetworkProfile = $serializer->deserialize($request->getContent(), SocialNetworkProfile::class, 'json');

        $newSocialNetworkProfile
            ->setCity($city)
            ->setCreatedAt(new \DateTime());

        $manager = $managerRegistry->getManager();
        $manager->persist($newSocialNetworkProfile);
        $manager->flush();

        return new JsonResponse($serializer->serialize($newSocialNetworkProfile, 'json'), JsonResponse::HTTP_CREATED, [], true);
    }
}
