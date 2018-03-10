<?php

namespace Criticalmass\Bundle\AppBundle\Controller\City;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Traits\ViewStorageTrait;
use Criticalmass\Component\SeoPage\SeoPage;
use Criticalmass\Component\ViewStorage\ViewStorageCache;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends AbstractController
{
    public function missingStatsAction($citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $rides = $this->getRideRepository()->findRidesWithoutStatisticsForCity($city);

        return $this->render(
            'AppBundle:City:missing_stats.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }

    protected function findNearCities(City $city)
    {
        /** @var FinderInterface $finder */
        $finder = $this->container->get('fos_elastica.finder.criticalmass_city.city');

        $enabledQuery = new \Elastica\Query\Term(['isEnabled' => true]);

        $selfTerm = new \Elastica\Query\Term(['id' => $city->getId()]);
        $selfQuery = new \Elastica\Query\BoolQuery();
        $selfQuery->addMustNot($selfTerm);

        $geoQuery = new \Elastica\Query\GeoDistance('pin', [
            'lat' => $city->getLatitude(),
            'lon' => $city->getLongitude(),
        ],
            '50km'
        );

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($geoQuery)
            ->addMust($enabledQuery)
            ->addMust($selfQuery);

        $query = new \Elastica\Query($boolQuery);

        $query->setSize(15);
        $query->setSort([
            '_geo_distance' => [
                'pin' => [
                    $city->getLatitude(),
                    $city->getLongitude(),
                ],
                'order' => 'desc',
                'unit' => 'km',
            ]
        ]);

        $results = $finder->find($query);

        return $results;
    }

    public function listRidesAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render('AppBundle:City:ride_list.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }

    public function listGalleriesAction(Request $request, SeoPage $seoPage, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $seoPage->setDescription('Übersicht über Fotos von Critical-Mass-Touren aus ' . $city->getCity());

        $result = $this->getPhotoRepository()->findRidesWithPhotoCounter($city);

        return $this->render('AppBundle:City:gallery_list.html.twig',
            [
                'city' => $city,
                'result' => $result
            ]
        );
    }

    public function showAction(Request $request, SeoPage $seoPage, ViewStorageCache $viewStorageCache, string $citySlug): Response
    {
        $city = $this->getCityBySlug($citySlug);

        if (!$city) {
            return $this->forward('AppBundle:City/MissingCity:missing', ['citySlug' => $citySlug]);
        }

        if (!$city->getEnabled()) {
            throw new NotFoundHttpException('Wir konnten keine Stadt unter der Bezeichnung "' . $citySlug . '" finden :(');
        }

        $viewStorageCache->countView($city);

        $blocked = $this->getBlockedCityRepository()->findCurrentCityBlock($city);

        if ($blocked) {
            return $this->render('AppBundle:City:blocked.html.twig', [
                'city' => $city,
                'blocked' => $blocked
            ]);
        }

        $nearCities = $this->findNearCities($city);

        $currentRide = $this->getRideRepository()->findCurrentRideForCity($city);

        $dateTime = null;

        if ($city->getTimezone()) {
            $dateTime = new \DateTime();
            $dateTime->setTimezone(new \DateTimeZone($city->getTimezone()));
        }

        $locations = $this->getLocationRepository()->findLocationsByCity($city);

        $photos = $this->getPhotoRepository()->findSomePhotos(8, null, $city);

        $seoPage
            ->setDescription('Informationen, Tourendaten, Tracks und Fotos von der Critical Mass in ' . $city->getCity())
            ->setPreviewPhoto($city)
            ->setCanonicalForObject($city)
            ->setTitle($city->getTitle());

        return $this->render('AppBundle:City:show.html.twig', [
            'city' => $city,
            'currentRide' => $currentRide,
            'dateTime' => $dateTime,
            'nearCities' => $nearCities,
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
