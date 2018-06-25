<?php

namespace AppBundle\Controller\Ride;

use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\SeoPage\SeoPage;
use AppBundle\Criticalmass\ViewStorage\ViewStorageCache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Weather;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RideController extends AbstractController
{
    public function listAction(): Response
    {
        $ridesResult = $this->getRideRepository()->findRidesInInterval();

        $rides = [];

        foreach ($ridesResult as $ride) {
            $rides[$ride->getFormattedDate()][] = $ride;
        }

        return $this->render('AppBundle:Ride:list.html.twig', [
            'rides' => $rides,
        ]);
    }

    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function showAction(Request $request, SeoPage $seoPage, ViewStorageCache $viewStorageCache, Ride $ride): Response
    {
        $nextRide = $this->getRideRepository()->getNextRide($ride);
        $previousRide = $this->getRideRepository()->getPreviousRide($ride);

        $viewStorageCache->countView($ride);

        $seoPage
            ->setDescription('Informationen, Strecken und Fotos von der Critical Mass in ' . $ride->getCity()->getCity() . ' am ' . $ride->getDateTime()->format('d.m.Y'))
            ->setCanonicalForObject($ride);

        if ($ride->getImageName()) {
            $seoPage->setPreviewPhoto($ride);
        } elseif ($ride->getFeaturedPhoto()) {
            $seoPage->setPreviewPhoto($ride->getFeaturedPhoto());
        }

        if ($ride->getSocialDescription()) {
            $seoPage->setDescription($ride->getSocialDescription());
        } elseif ($ride->getDescription()) {
            $seoPage->setDescription($ride->getDescription());
        }

        /**
         * @var Weather $weather
         */
        $weather = $this->getWeatherRepository()->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = round($weather->getTemperatureEvening()) . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        if ($this->getUser()) {
            $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(),
                $ride);
        } else {
            $participation = null;
        }

        return $this->render('AppBundle:Ride:show.html.twig', [
            'city' => $ride->getCity(),
            'ride' => $ride,
            'tracks' => $this->getTrackRepository()->findTracksByRide($ride),
            'photos' => $this->getPhotoRepository()->findPhotosByRide($ride),
            'subrides' => $this->getSubrideRepository()->getSubridesForRide($ride),
            'nextRide' => $nextRide,
            'previousRide' => $previousRide,
            'dateTime' => new \DateTime(),
            'weatherForecast' => $weatherForecast,
            'participation' => $participation,
        ]);
    }
}
