<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Location;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
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
                'rides' => $rides
            ]
        );
    }

    protected function findRidesForLocation(Location $location)
    {
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
