<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Weather;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends BaseController
{
    /**
     * Add weather data to a specific ride.
     *
     * @Operation(
     *     tags={"Weather"},
     *     summary="Add weather data for a ride",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("ride", class="App:Ride")
     * @Route("/{citySlug}/{rideIdentifier}/weather", name="caldera_criticalmass_rest_weather_add", methods={"PUT"})
     */
    public function addWeatherAction(Request $request, Ride $ride, ManagerRegistry $managerRegistry, SerializerInterface $serializer): JsonResponse
    {
        /** @var Weather $weather */
        $weather = $this->deserializeRequest($request, $serializer, Weather::class);

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime());

        $manager = $managerRegistry->getManager();
        $manager->persist($weather);
        $manager->flush();

        return new JsonResponse($serializer->serialize($weather, 'json'), JsonResponse::HTTP_OK, [], true);
    }
}
