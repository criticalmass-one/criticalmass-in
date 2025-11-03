<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Weather;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Weather')]
class WeatherController extends BaseController
{
    #[Route(
        path: '/{citySlug}/{rideIdentifier}/weather',
        name: 'caldera_criticalmass_rest_weather_add',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/{citySlug}/{rideIdentifier}/weather',
        summary: 'Add weather data to a specific ride',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                description: 'Provide a city slug for the ride’s city.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'rideIdentifier',
                description: 'Identifier of the ride.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Serialized weather data.',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Weather::class))
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 400, description: 'Invalid payload'),
            new OA\Response(response: 404, description: 'Ride not found'),
        ]
    )]
    public function addWeatherAction(Request $request, Ride $ride): JsonResponse
    {
        /** @var Weather $weather */
        $weather = $this->deserializeRequest($request, Weather::class);

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime());

        $manager = $this->managerRegistry->getManager();
        $manager->persist($weather);
        $manager->flush();

        return $this->createStandardResponse($weather, null, JsonResponse::HTTP_CREATED);
    }
}
