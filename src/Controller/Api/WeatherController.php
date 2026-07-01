<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Weather;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WeatherController extends BaseController
{
    /**
     * Add weather data to a specific ride.
     *
     * Provide weather data in the following format:
     *
     * <pre>{
     *   "temperatureMin": 12.5,
     *   "temperatureMax": 18.3,
     *   "temperatureMorning": 14.0,
     *   "temperatureDay": 17.5,
     *   "temperatureEvening": 16.0,
     *   "temperatureNight": 13.0,
     *   "weather": "Clear",
     *   "weatherDescription": "clear sky",
     *   "weatherIcon": "01d",
     *   "pressure": 1013.25,
     *   "humidity": 65,
     *   "windSpeed": 3.5,
     *   "windDeg": 180,
     *   "clouds": 10,
     *   "rain": 0.0
     * }</pre>
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/weather', name: 'caldera_criticalmass_rest_weather_add', methods: ['PUT'], priority: 190)]
    #[OA\Tag(name: 'Weather')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the weather data', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 201, description: 'Returned when weather data was successfully created')]
    public function addWeatherAction(Request $request, Ride $ride): JsonResponse
    {
        /** @var Weather $weather */
        $weather = $this->deserializeRequest($request, Weather::class, ['groups' => ['weather']]);

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime());

        $manager = $this->managerRegistry->getManager();
        $manager->persist($weather);
        $manager->flush();

        return $this->createStandardResponse($ride, [], JsonResponse::HTTP_CREATED);
    }

    /**
     * Lists the weather entries of a ride, including their ids.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/weather', name: 'caldera_criticalmass_rest_weather_list', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Weather')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listWeatherAction(Ride $ride): JsonResponse
    {
        $weathers = $this->managerRegistry->getRepository(Weather::class)->findBy(['ride' => $ride]);

        return $this->createStandardResponse($weathers, ['groups' => ['weather']]);
    }

    /**
     * Updates an existing weather entry.
     */
    #[Route(path: '/api/weather/{weatherId}', name: 'caldera_criticalmass_rest_weather_update', requirements: ['weatherId' => '\d+'], methods: ['POST'], priority: 200)]
    #[OA\Tag(name: 'Weather')]
    #[OA\Parameter(name: 'weatherId', in: 'path', description: 'Id of the weather entry', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(description: 'JSON representation of the weather data', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 404, description: 'Returned when the weather entry does not exist')]
    public function updateWeatherAction(Request $request, int $weatherId): JsonResponse
    {
        $weather = $this->managerRegistry->getRepository(Weather::class)->find($weatherId);

        if (!$weather) {
            throw new NotFoundHttpException('Weather entry not found');
        }

        $this->deserializeRequestInto($request, $weather, ['groups' => ['weather']]);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($weather, ['groups' => ['weather']]);
    }

    /**
     * Deletes a weather entry.
     */
    #[Route(path: '/api/weather/{weatherId}', name: 'caldera_criticalmass_rest_weather_delete', requirements: ['weatherId' => '\d+'], methods: ['DELETE'], priority: 200)]
    #[OA\Tag(name: 'Weather')]
    #[OA\Parameter(name: 'weatherId', in: 'path', description: 'Id of the weather entry', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    #[OA\Response(response: 404, description: 'Returned when the weather entry does not exist')]
    public function deleteWeatherAction(int $weatherId): JsonResponse
    {
        $weather = $this->managerRegistry->getRepository(Weather::class)->find($weatherId);

        if (!$weather) {
            throw new NotFoundHttpException('Weather entry not found');
        }

        $manager = $this->managerRegistry->getManager();
        $manager->remove($weather);
        $manager->flush();

        return new JsonResponse(['status' => 'ok', 'deletedWeatherId' => $weatherId]);
    }
}
