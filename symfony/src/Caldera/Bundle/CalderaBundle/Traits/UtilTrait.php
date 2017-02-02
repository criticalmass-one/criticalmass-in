<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Traits;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\CitySlug;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\HtmlMetadata\Metadata;
use Caldera\Bundle\CriticalmassCoreBundle\Router\ObjectRouter;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UtilTrait
{
    /**
     * Returns a city entity identified by its slug.
     *
     * @param $citySlug
     * @return City
     * @throws NotFoundHttpException
     */
    protected function getCityBySlug($citySlug): City
    {
        /** @var CitySlug $citySlug */
        $citySlug = $this->getCitySlugRepository()->findOneBySlug($citySlug);

        if ($citySlug) {
            return $citySlug->getCity();
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @return Metadata
     */
    protected function getMetadata(): Metadata
    {
        return $this->get('caldera.html_metadata');
    }

    protected function getCheckedCity($citySlug): City
    {
        $city = $this->getCityBySlug($citySlug);

        if (!$city) {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit ' . $citySlug . ' identifiziert.');
        }

        return $city;
    }

    /**
     * Returns a ride entity for a city entity and a datetime parameter. Will throw an exception if the ride does not exist.
     *
     * @param City $city
     * @param \DateTime $rideDateTime
     * @throws NotFoundHttpException
     * @return Ride
     */
    protected function getCheckedRide(City $city, \DateTime $rideDateTime): Ride
    {
        /** @var Ride $ride */
        $ride = $this->getRideRepository()->findCityRideByDate($city, $rideDateTime);

        if (!$ride) {
            throw new NotFoundHttpException('Wir haben leider keine Tour in ' . $city->getCity() . ' am ' . $rideDateTime->format('d. m. Y') . ' gefunden.');
        }

        return $ride;
    }

    protected function getCheckedDateTime(string $dateTime): \DateTime
    {
        try {
            $dateTime = new \DateTime($dateTime);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Mit diesem Datum können wir leider nichts anfange. Bitte gib ein Datum im Format YYYY-MM-DD an.');
        }

        return $dateTime;
    }

    protected function getCheckedCitySlugRideDateRide(string $citySlug, string $dateTime): Ride
    {
        /** @var City $city */
        $city = $this->getCheckedCity($citySlug);

        /** @var \DateTime $rideDateTime */
        $rideDateTime = $this->getCheckedDateTime($dateTime);

        /** @var Ride $ride */
        $ride = $this->getCheckedRide($city, $rideDateTime);

        return $ride;
    }

    protected function getSession(): Session
    {
        $session = new Session();

        return $session;
    }

    protected function generateObjectUrl($object, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        /** @var ObjectRouter $router */
        $router = $this->get('caldera.criticalmass.routing.object_router');

        $url = $router->generate($object, $referenceType);

        return $url;
    }

    protected function redirectToObject($object, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $url = $this->generateObjectUrl($object, $referenceType);

        return $this->redirect($url);
    }
}