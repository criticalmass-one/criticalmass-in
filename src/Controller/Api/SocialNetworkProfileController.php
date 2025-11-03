<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Social Network Profile')]
class SocialNetworkProfileController extends BaseController
{
    #[Route(
        path: '/socialnetwork-profiles',
        name: 'caldera_criticalmass_rest_socialnetwork_profiles_list',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/socialnetwork-profiles',
        summary: 'Search for social network profiles',
        parameters: [
            new OA\Parameter(
                name: 'networkIdentifier',
                in: 'query',
                description: 'Limit results to the specified social network (e.g. instagram, facebook)',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'autoFetch',
                in: 'query',
                description: 'Filter by auto-fetch flag (true/false)',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'entities',
                in: 'query',
                description: 'Comma-separated list of entity class names to filter by',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'App\\Entity\\City,App\\Entity\\Ride')
            ),
            new OA\Parameter(
                name: 'citySlug',
                in: 'query',
                description: 'Optionally limit profiles to a specific city (slug)',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listSocialNetworkProfilesAction(Request $request): JsonResponse
    {
        $networkIdentifier = $request->query->get('networkIdentifier');
        $autoFetch = $request->query->has('autoFetch') ? $request->query->getBoolean('autoFetch') : null;

        $entities = $request->query->get('entities');
        $entityClassNames = $entities ? array_filter(array_map('trim', explode(',', $entities))) : [];

        $city = null;
        if ($citySlug = $request->query->get('citySlug')) {
            $city = $this->managerRegistry->getRepository(City::class)->findOneBy(['slug' => $citySlug]);
        }

        $profileList = $this->managerRegistry
            ->getRepository(SocialNetworkProfile::class)
            ->findByProperties($networkIdentifier, $autoFetch, $city, $entityClassNames);

        return $this->createStandardResponse($profileList);
    }

    #[Route(
        path: '/{citySlug}/socialnetwork-profiles',
        name: 'caldera_criticalmass_rest_socialnetwork_profiles_citylist',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/{citySlug}/socialnetwork-profiles',
        summary: 'Retrieve a list of social network profiles assigned to a city',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Slug of the city',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 404, description: 'City not found'),
        ]
    )]
    public function listSocialNetworkProfilesCityAction(City $city): JsonResponse
    {
        $profileList = $this->managerRegistry
            ->getRepository(SocialNetworkProfile::class)
            ->findByCity($city);

        return $this->createStandardResponse($profileList);
    }

    #[Route(
        path: '/{citySlug}/socialnetwork-profiles/{id}',
        name: 'caldera_criticalmass_rest_socialnetwork_profiles_update',
        methods: ['POST']
    )]
    #[OA\Post(
        path: '/{citySlug}/socialnetwork-profiles/{id}',
        summary: 'Update properties of a social network profile',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Slug of the city the profile belongs to',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Profile ID',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Serialized SocialNetworkProfile content',
            required: true,
            content: new OA\JsonContent(type: 'object')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 404, description: 'Profile not found'),
        ]
    )]
    public function updateSocialNetworkProfileAction(
        Request $request,
        SocialNetworkProfile $socialNetworkProfile,
        EntityMergerInterface $entityMerger
    ): JsonResponse {
        /** @var SocialNetworkProfile $updatedSocialNetworkProfile */
        $updatedSocialNetworkProfile = $this->serializer->deserialize(
            $request->getContent(),
            SocialNetworkProfile::class,
            'json'
        );

        $entityMerger->merge($updatedSocialNetworkProfile, $socialNetworkProfile);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($socialNetworkProfile);
    }

    #[Route(
        path: '/{citySlug}/socialnetwork-profiles',
        name: 'caldera_criticalmass_rest_socialnetwork_profiles_create',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/{citySlug}/socialnetwork-profiles',
        summary: 'Create a new social network profile and assign it to the provided city',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Slug of the city',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Serialized SocialNetworkProfile content',
            required: true,
            content: new OA\JsonContent(type: 'object')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function createSocialNetworkProfileAction(Request $request, City $city): JsonResponse
    {
        /** @var SocialNetworkProfile $newSocialNetworkProfile */
        $newSocialNetworkProfile = $this->serializer->deserialize(
            $request->getContent(),
            SocialNetworkProfile::class,
            'json'
        );

        $newSocialNetworkProfile
            ->setCity($city)
            ->setCreatedAt(new \DateTime());

        $manager = $this->managerRegistry->getManager();
        $manager->persist($newSocialNetworkProfile);
        $manager->flush();

        return $this->createStandardResponse($newSocialNetworkProfile);
    }
}
