<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
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
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("city", class="App:City", isOptional="true")
     */
    #[Route(path: '/socialnetwork-profiles', name: 'caldera_criticalmass_rest_socialnetwork_profiles_list', methods: ['GET'])]
    public function listSocialNetworkProfilesAction(Request $request, City $city = null): JsonResponse
    {
        $networkIdentifier = $request->get('networkIdentifier');
        $autoFetch = (bool)$request->get('autoFetch');

        if ($entities = $request->get('entities')) {
            $entityClassNames = explode(',', $entities);
        } else {
            $entityClassNames = [];
        }

        $profileList = $this->managerRegistry->getRepository(SocialNetworkProfile::class)->findByProperties($networkIdentifier, $autoFetch, $city, $entityClassNames);

        return $this->createStandardResponse($profileList);
    }

    /**
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Retrieve a list of social network profiles assigned to a city",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("city", class="App:City")
     */
    #[Route(path: '/{citySlug}/socialnetwork-profiles', name: 'caldera_criticalmass_rest_socialnetwork_profiles_citylist', methods: ['GET'])]
    public function listSocialNetworkProfilesCityAction(City $city): JsonResponse
    {
        $profileList = $this->managerRegistry->getRepository(SocialNetworkProfile::class)->findByCity($city);

        return $this->createStandardResponse($profileList);
    }

    /**
     * Update properties of a social network profile.
     *
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Update properties of a social network profile",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("socialNetworkProfile", class="App:SocialNetworkProfile")
     */
    #[Route(path: '/{citySlug}/socialnetwork-profiles/{profileId}', name: 'caldera_criticalmass_rest_socialnetwork_profiles_update', methods: ['POST'])]
    public function updateSocialNetworkProfileAction(Request $request, SocialNetworkProfile $socialNetworkProfile, EntityMergerInterface $entityMerger): Response
    {
        $updatedSocialNetworkProfile = $this->serializer->deserialize($request->getContent(), SocialNetworkProfile::class, 'json');

        $entityMerger->merge($updatedSocialNetworkProfile, $socialNetworkProfile);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($socialNetworkProfile);
    }

    /**
     * Create a new social network profile and assign it to the provided city.
     *
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Create a new social network profile",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("city", class="App:City")
     */
    #[Route(path: '/{citySlug}/socialnetwork-profiles', name: 'caldera_criticalmass_rest_socialnetwork_profiles_create', methods: ['PUT'])]
    public function createSocialNetworkProfileAction(Request $request, City $city): JsonResponse
    {
        $newSocialNetworkProfile = $this->serializer->deserialize($request->getContent(), SocialNetworkProfile::class, 'json');

        $newSocialNetworkProfile
            ->setCity($city)
            ->setCreatedAt(new \DateTime());

        $manager = $this->managerRegistry->getManager();
        $manager->persist($newSocialNetworkProfile);
        $manager->flush();

        return $this->createStandardResponse($newSocialNetworkProfile);
    }
}
