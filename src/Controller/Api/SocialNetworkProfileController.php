<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialNetworkProfileController extends BaseController
{
    /**
     * Search for social network profiles.
     *
     * You can filter the results by providing optional query parameters.
     *
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Search for social network profiles",
     *     @OA\Parameter(
     *         name="networkIdentifier",
     *         in="query",
     *         description="Filter by social network identifier (e.g. twitter, facebook, instagram)",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="autoFetch",
     *         in="query",
     *         description="Filter by auto-fetch setting",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *     ),
     *     @OA\Parameter(
     *         name="entities",
     *         in="query",
     *         description="Comma-separated list of entity class names to filter by",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/api/socialnetwork-profiles', name: 'caldera_criticalmass_rest_socialnetwork_profiles_list', methods: ['GET'])]
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
     * Retrieve a list of social network profiles assigned to a city.
     *
     * @Operation(
     *     tags={"Social Network Profile"},
     *     summary="Retrieve a list of social network profiles assigned to a city",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-profiles', name: 'caldera_criticalmass_rest_socialnetwork_profiles_citylist', methods: ['GET'])]
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
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of the social network profile to update",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         description="JSON representation of the profile properties to update",
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-profiles/{id}', name: 'caldera_criticalmass_rest_socialnetwork_profiles_update', methods: ['POST'])]
    public function updateSocialNetworkProfileAction(
        Request $request,
        SocialNetworkProfile $socialNetworkProfile,
        EntityMergerInterface $entityMerger
    ): Response {
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
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\RequestBody(
     *         description="JSON representation of the social network profile to create",
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successfully created"
     *     )
     * )
     */
    #[Route(path: '/api/{citySlug}/socialnetwork-profiles', name: 'caldera_criticalmass_rest_socialnetwork_profiles_create', methods: ['PUT'])]
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
