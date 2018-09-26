<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Search;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Response;

class PrefetchController extends AbstractController
{
    public function prefetchAction(): Response
    {
        $prefetchedData = array_merge($this->getCitiesResult(), $this->getRidesResult());

        return new Response(json_encode($prefetchedData), 200, [
            'Content-Type' => 'text/json'
        ]);
    }

    protected function getCitiesResult(): array
    {
        $cities = $this->getCityRepository()->findEnabledCities();
        $result = [];

        /** @var City $city */
        foreach ($cities as $city) {
            $result[] = [
                'type' => 'city',
                'url' => $this->generateObjectUrl($city),
                'value' => $city->getCity()
            ];
        }

        return $result;
    }

    protected function getRidesResult(): array
    {
        $rides = $this->getRideRepository()->findCurrentRides();
        $result = [];

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $result[] = [
                'type' => 'ride',
                'url' => $this->generateObjectUrl($ride),
                'value' => $ride->getFancyTitle(),
                'meta' => [
                    'dateTime' => $ride->getDateTime()->format('Y-m-d\TH:i:s'),
                    'location' => ($ride->getHasLocation() ? $ride->getLocation() : '')
                ]
            ];
        }

        return $result;
    }
}
