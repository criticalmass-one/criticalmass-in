<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Weather;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function addWeatherAction(Request $request, Ride $ride, ManagerRegistry $managerRegistry, SerializerInterface $serializer): Response
    {
        /** @var Weather $weather */
        $weather = $this->deserializeRequest($request, $serializer, Weather::class);

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime());

        $manager = $managerRegistry->getManager();
        $manager->persist($weather);
        $manager->flush();

        $context = new Context();

        $view = View::create();
        $view
            ->setData($weather)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_CREATED)
            ->setContext($context);

        return $this->handleView($view);
    }
}
