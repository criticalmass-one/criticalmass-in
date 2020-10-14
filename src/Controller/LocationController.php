<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\Ride;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function listlocationsAction(City $city): Response
    {
        $locations = $this->getLocationRepository()->findLocationsByCity($city);

        return $this->render('Location/list.html.twig', [
            'locations' => $locations,
        ]);
    }

    /**
     * @ParamConverter("location", class="App:Location")
     */
    public function showAction(Location $location): Response
    {
        $rides = $this->findRidesForLocation($location);

        $locations = $this->getLocationRepository()->findLocationsByCity($location->getCity());

        return $this->render('Location/show.html.twig', [
            'location' => $location,
            'locations' => $locations,
            'rides' => $rides,
            'ride' => null,
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function rideAction(Ride $ride): Response
    {
        $location = $this->getLocationRepository()->findLocationForRide($ride);

        if (!$location) {
            throw new NotFoundHttpException();
        }

        $rides = $this->findRidesForLocation($location);

        $locations = $this->getLocationRepository()->findLocationsByCity($ride->getCity());

        return $this->render('Location/show.html.twig', [
            'location' => $location,
            'locations' => $locations,
            'rides' => $rides,
            'ride' => $ride,
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
            'dateTime'
        ]);

        $result = $finder->find($query);

        return $result;
    }
}
