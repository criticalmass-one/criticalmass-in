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
     * @Operation(
     *     tags={"Weather"},
     *     summary="Add weather data for a ride",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/{citySlug}/{rideIdentifier}/weather', name: 'caldera_criticalmass_rest_weather_add', methods: ['PUT'])]
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
