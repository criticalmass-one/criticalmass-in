<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Weather;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

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
     *
     * @Operation(
     *     tags={"Weather"},
     *     summary="Add weather data for a ride",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="rideIdentifier",
     *         in="path",
     *         description="Identifier of the ride (date or slug)",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\RequestBody(
     *         description="JSON representation of the weather data",
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returned when weather data was successfully created"
     *     )
     * )
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/weather', name: 'caldera_criticalmass_rest_weather_add', methods: ['PUT'])]
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

        return $this->createStandardResponse($ride, null, JsonResponse::HTTP_CREATED);
    }
}
