<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Weather;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Weather")]
class WeatherController extends BaseController
{
    /**
     * Add weather data to a specific ride.
     * @ParamConverter("ride", class="App:Ride")
     */
    #[OA\Response(
        response: 200,
        description: "Returned when successful"
    )]
    #[OA\Parameter(
        name: 'citySlug',
        description: 'Provide a city slug for the ride’s city',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'rideIdentifier',
        description: 'Identifier of the ride',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        description: "Serialized weather data",
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Weather::class))
    )]
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
