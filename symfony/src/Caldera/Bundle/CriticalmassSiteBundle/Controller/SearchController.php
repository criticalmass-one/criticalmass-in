<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function queryAction(Request $request) {
        $result = [
            'Hamburg',
            'Critical Mass Hamburg',
            'Critical Mass Hamburg (29. Januar 2016)'
        ];

        return new Response(
            json_encode($result),
            200,
            [
                'Content-Type' => 'text/json'
            ]
        );
    }

    public function prefetchAction(Request $request) {
        $result = [];

        $rides = $this->getRideRepository()->findCurrentRides();

        foreach ($rides as $ride) {
            $result[] = [
                'type' => 'ride',
                'url' => $this->generateUrl($ride),
                'value' => $ride->getFancyTitle()
            ];
        }

        $cities = $this->getCityRepository()->findEnabledCities();

        foreach ($cities as $city) {
            $result[] = [
                'type' => 'city',
                'url' => $this->generateUrl($city),
                'value' => $city->getCity()
            ];
        }

        return new Response(
            json_encode($result),
            200,
            [
                'Content-Type' => 'text/json'
            ]
        );
    }
}
