<?php declare(strict_types=1);

namespace App\Controller\Search;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use Symfony\Component\HttpFoundation\Response;

class PrefetchController extends AbstractController
{
    public function prefetchAction(ObjectRouterInterface $objectRouter): Response
    {
        $result = [];

        $rides = $this->getRideRepository()->findCurrentRides();

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $result[] = [
                'type' => 'ride',
                'url' => $objectRouter->generate($ride),
                'value' => $ride->getTitle(),
                'meta' => [
                    'dateTime' => $ride->getDateTime()->format('Y-m-d\TH:i:s'),
                    'location' => $ride->getLocation() ?? '',
                ]
            ];
        }

        $cities = $this->getCityRepository()->findEnabledCities();

        /** @var City $city */
        foreach ($cities as $city) {
            $result[] = [
                'type' => 'city',
                'url' => $objectRouter->generate($city),
                'value' => $city->getCity()
            ];
        }

        return new Response(json_encode($result), 200, [
            'Content-Type' => 'text/json'
        ]);
    }
}
