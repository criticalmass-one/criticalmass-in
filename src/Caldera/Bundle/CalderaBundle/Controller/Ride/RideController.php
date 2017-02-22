<?php

namespace Caldera\Bundle\CalderaBundle\Controller\Ride;

use Caldera\Bundle\CalderaBundle\Controller\AbstractController;
use Caldera\Bundle\CalderaBundle\Entity\RideEstimate;
use Caldera\Bundle\CalderaBundle\Entity\Weather;
use Caldera\Bundle\CalderaBundle\Traits\ViewStorageTrait;
use Caldera\Bundle\CalderaBundle\Form\Type\RideEstimateType;
use Caldera\Bundle\CalderaBundle\Statistic\RideEstimate\RideEstimateService;
use Symfony\Component\HttpFoundation\Request;

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
            'CalderaBundle:Ride:list.html.twig',
            array(
                'rides' => $rides
            )
        );
    }

    public function showAction(Request $request, $citySlug, $rideDate)
    {
        $city = $this->getCheckedCity($citySlug);
        $rideDateTime = $this->getCheckedDateTime($rideDate);
        $ride = $this->getCheckedRide($city, $rideDateTime);

        $nextRide = $this->getRideRepository()->getNextRide($ride);
        $previousRide = $this->getRideRepository()->getPreviousRide($ride);

        $this->countRideView($ride);

        $this
            ->getMetadata()
            ->setDescription('Informationen, Strecken und Fotos von der Critical Mass in ' . $city->getCity() . ' am ' . $ride->getDateTime()->format('d.m.Y'));

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
            $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(), $ride);
        } else {
            $participation = null;
        }

        return $this->render(
            'CalderaBundle:Ride:show.html.twig',
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

    public function addestimateAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $rideEstimate = new RideEstimate();
        $rideEstimate->setUser($this->getUser());
        $rideEstimate->setRide($ride);

        $estimateForm = $this->createForm(
            RideEstimateType::class,
            $rideEstimate,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_ride_addestimate',
                    [
                        'citySlug' => $ride->getCity()->getMainSlugString(),
                        'rideDate' => $ride->getFormattedDate()
                    ]
                )
            ]
        );

        $estimateForm->handleRequest($request);

        if ($estimateForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estimateForm->getData());
            $em->flush();

            /**
             * @var RideEstimateService $estimateService
             */
            $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate');
            $estimateService->calculateEstimates($ride);
        }

        return $this->redirectToObject($ride);
    }
}
