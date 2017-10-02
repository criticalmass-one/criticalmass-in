<?php

namespace AppBundle\Controller\Ride;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\RideEstimate;
use AppBundle\Entity\Weather;
use AppBundle\Traits\ViewStorageTrait;
use AppBundle\Form\Type\RideEstimateType;
use AppBundle\Statistic\RideEstimate\RideEstimateService;
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

    public function showAction(Request $request, $citySlug, $rideDate)
    {
        $city = $this->getCheckedCity($citySlug);
        $rideDateTime = $this->getCheckedDateTime($rideDate);
        $ride = $this->getCheckedRide($city, $rideDateTime);

        $nextRide = $this->getRideRepository()->getNextRide($ride);
        $previousRide = $this->getRideRepository()->getPreviousRide($ride);

        $this->countRideView($ride);

        $this
            ->getSeoPage()
            ->setDescription('Informationen, Strecken und Fotos von der Critical Mass in ' . $city->getCity() . ' am ' . $ride->getDateTime()->format('d.m.Y'))
            ->setCanonicalForObject($ride)
        ;

        if ($ride->getImageName()) {
            $this->getSeoPage()->setPreviewPhoto($ride);
        } elseif ($ride->getFeaturedPhoto()) {
            $this->getSeoPage()->setPreviewPhoto($ride->getFeaturedPhoto());
        }

        if ($ride->getSocialDescription()) {
            $this->getSeoPage()->setDescription($ride->getSocialDescription());
        } elseif ($ride->getDescription()) {
            $this->getSeoPage()->setDescription($ride->getDescription());
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
            $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(), $ride);
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

    /**
     * @Security("has_role('ROLE_USER')")
     */
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
