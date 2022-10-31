<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\ElasticCityFinder\ElasticCityFinderInterface;
use App\Entity\City;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Event\View\ViewEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function missingStatsAction(City $city): Response
    {
        return $this->render('City/missing_stats.html.twig', [
            'city' => $city,
            'rides' => $this->getRideRepository()->findRidesWithoutStatisticsForCity($city),
        ]);
    }

    /**
     * @ParamConverter("city", class="App:City")
     */
    public function listRidesAction(City $city): Response
    {
        return $this->render('City/ride_list.html.twig', [
            'city' => $city,
            'rides' => $this->getRideRepository()->findRidesForCity($city),
        ]);
    }

    /**
     * @ParamConverter("city", class="App:City")
     */
    public function listGalleriesAction(Request $request, SeoPageInterface $seoPage, City $city): Response
    {
        $seoPage->setDescription('Übersicht über Fotos von Critical-Mass-Touren aus ' . $city->getCity());

        $result = $this->getPhotoRepository()->findRidesWithPhotoCounter($city);

        return $this->render('City/gallery_list.html.twig', [
            'city' => $city,
            'result' => $result,
        ]);
    }

    /**
     * @ParamConverter("city", class="App:City", isOptional=true)
     */
    public function showAction(Request $request, ElasticCityFinderInterface $elasticCityFinder, SeoPageInterface $seoPage, EventDispatcherInterface $eventDispatcher, City $city = null): Response
    {
        if (!$city) {
            $citySlug = $request->get('citySlug');

            if (!$citySlug) {
                throw $this->createNotFoundException('City not found');
            }

            return $this->forward('App\\Controller\\City\\MissingCityController::missingAction', [
                'citySlug' => $citySlug,
            ]);
        }

        $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($city));

        $blocked = $this->getBlockedCityRepository()->findCurrentCityBlock($city);

        if ($blocked) {
            return $this->render('City/blocked.html.twig', [
                'city' => $city,
                'blocked' => $blocked
            ]);
        }
        
        $seoPage
            ->setDescription('Informationen, Tourendaten, Tracks und Fotos von der Critical Mass in ' . $city->getCity())
            ->setCanonicalForObject($city)
            ->setTitle($city->getTitle());

        if ($city->getImageName()) {
            $seoPage->setPreviewPhoto($city);
        } else {
            $seoPage->setPreviewMap($city);
        }

        return $this->render('City/show.html.twig', [
            'city' => $city,
            'currentRide' => $this->getRideRepository()->findCurrentRideForCity($city),
            'nearCities' => $elasticCityFinder->findNearCities($city),
            'locations' => $this->getLocationRepository()->findLocationsByCity($city),
            'photos' => $this->getPhotoRepository()->findSomePhotos(8, null, $city),
            'rides' => $this->getRideRepository()->findRidesForCity($city, 'DESC', 6),
            'socialNetworkProfiles' => $this->getSocialNetworkProfileRepository()->findByCity($city),
        ]);
    }

    /**
     * @ParamConverter("city", class="App:City")
     */
    public function getlocationsAction(City $city): Response
    {
        return new Response(json_encode($this->getRideRepository()->getLocationsForCity($city)), 200, [
            'Content-Type' => 'text/json',
        ]);
    }
}
