<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\Location;
use AppBundle\Entity\Ride;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationController extends AbstractController
{
    /**
     * @ParamConverter("city", class="AppBundle:City")
     */
    public function listlocationsAction(City $city): Response
    {
        $locations = $this->getLocationRepository()->findAll();

        return $this->render('CalderaCriticalmassDesktopBundle:Location:list.html.twig', [
            'locations' => $locations
        ]);
    }

    /**
     * @ParamConverter("location", class="AppBundle:Location", options={"slug": "locationSlug"})
     */
    public function showAction(Location $location): Response
    {
        $rides = $this->findRidesForLocation($location);

        $locations = $this->getLocationRepository()->findLocationsByCity($location->getCity());

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

    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function rideAction(Ride $ride): Response
    {
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
