<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Location;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;
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
            'CalderaCriticalmassSiteBundle:Location:show.html.twig',
            [
                'location' => $location,
                'locations' => $locations,
                'rides' => $rides,
                'ride' => null
            ]
        );
    }

    public function rideAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $location = $this->getLocationRepository()->findLocationForRide($ride);

        if (!$location) {
            throw new NotFoundHttpException();
        }

        $rides = $this->findRidesForLocation($location);

        $locations = $this->getLocationRepository()->findLocationsByCity($ride->getCity());

        return $this->render(
            'CalderaCriticalmassSiteBundle:Location:show.html.twig',
            [
                'location' => $location,
                'locations' => $locations,
                'rides' => $rides,
                'ride' => $ride
            ]
        );
    }

    protected function findRidesForLocation(Location $location)
    {
        if (!$location->getLatitude() || !$location->getLongitude()) {
            return false;
        }

        $finder = $this->container->get('fos_elastica.finder.criticalmass.ride');

        $archivedFilter = new \Elastica\Filter\Term(['isArchived' => false]);
        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $location->getLatitude(),
                'lon' => $location->getLongitude()
            ],
            '500m'
        );

        $filter = new \Elastica\Filter\BoolAnd([$archivedFilter, $geoFilter]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $filter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(25);
        $query->setSort(
            [
                'dateTime'
            ]
        );

        $result = $finder->find($query);

        return $result;
    }
}
