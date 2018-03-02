<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\Location;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationController extends AbstractController
{
    public function listlocationsAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $locations = $this->getLocationRepository()->findAll();

        return $this->render(
            'CalderaCriticalmassDesktopBundle:Location:list.html.twig',
            [
                'locations' => $locations
            ]
        );
    }

    public function showAction(Request $request, $citySlug, $locationSlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $location = $this->getLocationRepository()->findOneBySlug($locationSlug);

        if (!$location) {
            throw new NotFoundHttpException();
        }

        $rides = $this->findRidesForLocation($location);

        $locations = $this->getLocationRepository()->findLocationsByCity($city);

        return $this->render(
            'AppBundle:Location:show.html.twig',
            [
                'location' => $location,
                'locations' => $locations,
                'rides' => $rides,
                'ride' => null
            ]
        );
    }

    public function rideAction(Request $request, string $citySlug, string $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $location = $this->getLocationRepository()->findLocationForRide($ride);

        if (!$location) {
            throw new NotFoundHttpException();
        }

        $rides = $this->findRidesForLocation($location);

        $locations = $this->getLocationRepository()->findLocationsByCity($ride->getCity());

        return $this->render('AppBundle:Location:show.html.twig', [
            'location' => $location,
            'locations' => $locations,
            'rides' => $rides,
            'ride' => $ride
        ]);
    }

    protected function findRidesForLocation(Location $location): array
    {
        if (!$location->getLatitude() || !$location->getLongitude()) {
            return [];
        }

        /** @var FinderInterface $finder */
        $finder = $this->container->get('fos_elastica.finder.criticalmass_ride.ride');

        $geoQuery = new \Elastica\Query\GeoDistance('pin', [
            'lat' => $location->getLatitude(),
            'lon' => $location->getLongitude(),
        ],
            '500m'
        );

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($geoQuery);

        $query = new \Elastica\Query($boolQuery);

        $query->setSize(25);
        $query->setSort([
            'simpleDate'
        ]);

        $result = $finder->find($query);

        return $result;
    }
}
