<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Ride;

use Criticalmass\Component\SeoPage\SeoPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Weather;
use Criticalmass\Bundle\AppBundle\Traits\ViewStorageTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RideController extends AbstractController
{
    use ViewStorageTrait;

    public function listAction(Request $request)
    {
        $ridesResult = $this->getRideRepository()->findRidesInInterval();

        $rides = array();

        foreach ($ridesResult as $ride) {
            $rides[$ride->getFormattedDate()][] = $ride;
        }

        return $this->render(
            'AppBundle:Ride:list.html.twig',
            array(
                'rides' => $rides
            )
        );
    }

    public function showMonthAction(Request $request, string $citySlug, string $rideDate): Response
    {
        $city = $this->getCheckedCity($citySlug);
        $dateTime = new \DateTime(sprintf('%s-01', $rideDate));

        $rideList = $this->getRideRepository()->findByCityAndMonth($city, $dateTime);

        if (count($rideList) !== 1) {
            throw $this->createNotFoundException();
        }

        $ride = array_pop($rideList);

        return $this->redirectToObject($ride);
    }

    public function showAction(Request $request, SeoPage $seoPage, $citySlug, $rideDate)
    {
        $city = $this->getCheckedCity($citySlug);
        $rideDateTime = $this->getCheckedDateTime($rideDate);
        $ride = $this->getCheckedRide($city, $rideDateTime);

        $nextRide = $this->getRideRepository()->getNextRide($ride);
        $previousRide = $this->getRideRepository()->getPreviousRide($ride);

        $this->countRideView($ride);

        $seoPage
            ->setDescription('Informationen, Strecken und Fotos von der Critical Mass in ' . $city->getCity() . ' am ' . $ride->getDateTime()->format('d.m.Y'))
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

        return $this->render(
            'AppBundle:Ride:show.html.twig',
            array(
                'city' => $city,
                'ride' => $ride,
                'tracks' => $this->getTrackRepository()->findTracksByRide($ride),
                'photos' => $this->getPhotoRepository()->findPhotosByRide($ride),
                'subrides' => $this->getSubrideRepository()->getSubridesForRide($ride),
                'nextRide' => $nextRide,
                'previousRide' => $previousRide,
                'dateTime' => new \DateTime(),
                'weatherForecast' => $weatherForecast,
                'participation' => $participation
            )
        );
    }
}
