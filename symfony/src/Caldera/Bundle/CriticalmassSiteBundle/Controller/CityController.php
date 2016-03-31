<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\CitySlugGenerator\CitySlugGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\StandardCityType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CityType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Region;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends AbstractController
{
    public function listAction()
    {
        $cities = $this->getCityRepository()->findCities();

        $this->getMetadata()
            ->setDescription('Liste mit allen weltweiten Critical-Mass-Radtouren.');

        return $this->render('CalderaCriticalmassSiteBundle:City:list.html.twig', array('cities' => $cities));
    }

    protected function findNearCities(City $city)
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.city');

        $archivedFilter = new \Elastica\Filter\Term(['isArchived' => false]);
	    $enabledFilter = new \Elastica\Filter\Term(['isEnabled' => true]);
	    $selfFilter = new \Elastica\Filter\BoolNot(new \Elastica\Filter\Term(['id' => $city->getId()]));

        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $city->getLatitude(),
                'lon' => $city->getLongitude()
            ],
            '50km'
        );

        $filter = new \Elastica\Filter\BoolAnd([$archivedFilter, $geoFilter, $enabledFilter, $selfFilter]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $filter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(15);
        $query->setSort(
            [
                '_geo_distance' =>
                [
                    'pin' =>
                    [
                        $city->getLatitude(),
                        $city->getLongitude()
                    ],
                'order' => 'desc',
                'unit' => 'km'
                ]
            ]
        );

        $results = $finder->find($query);

        return $results;
    }

    public function listRidesAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render('CalderaCriticalmassSiteBundle:City:rideList.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }

    public function listGalleriesAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $this->getMetadata()->setDescription('Übersicht über Fotos von Critical-Mass-Touren aus '.$city->getCity());

        $result = $this->getPhotoRepository()->findRidesWithPhotoCounter($city);

        return $this->render('CalderaCriticalmassSiteBundle:City:galleryList.html.twig',
            [
                'city' => $city,
                'result' => $result
            ]
        );
    }

    public function showAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        if (!$city->getEnabled()) {
            throw new NotFoundHttpException('Wir konnten keine Stadt unter der Bezeichnung "' . $citySlug . '" finden :(');
        }

        $nearCities = $this->findNearCities($city);

        $currentRide = $this->getRideRepository()->findCurrentRideForCity($city);

        $dateTime = null;

        if ($city->getTimezone()) {
            $dateTime = new \DateTime();
            $dateTime->setTimezone(new \DateTimeZone($city->getTimezone()));
        }

        $events = $this->getEventRepository()->findEventsByCity($city);

        $locations = $this->getLocationRepository()->findLocationsByCity($city);

        $photos = $this->getPhotoRepository()->findSomePhotos(8, null, $city);

        $this->getMetadata()
            ->setDescription('Informationen, Tourendaten, Tracks und Fotos von der Critical Mass in '.$city->getCity());

        return $this->render('CalderaCriticalmassSiteBundle:City:show.html.twig', [
            'city' => $city,
            'currentRide' => $currentRide,
            'dateTime' => $dateTime,
            'nearCities' => $nearCities,
            'events' => $events,
            'locations' => $locations,
            'photos' => $photos
        ]);
    }

    public function liveAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);
        
        return $this->render('CalderaCriticalmassDesktopBundle:City:live.html.twig', array('city' => $city));
    }

    public function getlocationsAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $locations = $this->getRideRepository()->getLocationsForCity($city);

        return new Response
        (
            json_encode($locations),
            200,
            [
                'Content-Type' => 'text/json'
            ]
        );
    }
}
