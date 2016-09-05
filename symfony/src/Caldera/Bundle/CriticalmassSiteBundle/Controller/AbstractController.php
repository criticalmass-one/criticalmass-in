<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CalderaBundle\Repository\BlogPostRepository;
use Caldera\Bundle\CalderaBundle\Traits\RepositoryTrait;
use Caldera\Bundle\CriticalmassCoreBundle\Metadata\Metadata;
use Caldera\Bundle\CalderaBundle\Entity\AnonymousName;
use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Repository\AnonymousNameRepository;
use Caldera\Bundle\CalderaBundle\Repository\BoardRepository;
use Caldera\Bundle\CalderaBundle\Repository\CityRepository;
use Caldera\Bundle\CalderaBundle\Repository\ContentRepository;
use Caldera\Bundle\CalderaBundle\Repository\EventRepository;
use Caldera\Bundle\CalderaBundle\Repository\FacebookRidePropertiesRepository;
use Caldera\Bundle\CalderaBundle\Repository\IncidentRepository;
use Caldera\Bundle\CalderaBundle\Repository\LocationRepository;
use Caldera\Bundle\CalderaBundle\Repository\ParticipationRepository;
use Caldera\Bundle\CalderaBundle\Repository\PhotoRepository;
use Caldera\Bundle\CalderaBundle\Repository\PostRepository;
use Caldera\Bundle\CalderaBundle\Repository\RegionRepository;
use Caldera\Bundle\CalderaBundle\Repository\RideRepository;
use Caldera\Bundle\CalderaBundle\Repository\SubrideRepository;
use Caldera\Bundle\CalderaBundle\Repository\ThreadRepository;
use Caldera\Bundle\CalderaBundle\Repository\TrackRepository;
use Caldera\Bundle\CalderaBundle\Repository\WeatherRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractController extends Controller
{
    use RepositoryTrait;
    
    /**
     * Returns a city entity identified by its slug.
     *
     * @param $citySlug
     * @return City
     * @throws NotFoundHttpException
     */
    protected function getCityBySlug($citySlug)
    {
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
    protected function getMetadata()
    {
        return $this->get('caldera.criticalmass.metadata');
    }

    protected function getCheckedCity($citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        if (!$city) {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
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
    protected function getCheckedRide(City $city, \DateTime $rideDateTime)
    {
        $ride = $this->getRideRepository()->findCityRideByDate($city, $rideDateTime);

        if (!$ride) {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }

        return $ride;
    }

    protected function getCheckedDateTime($dateTime)
    {
        try {
            $dateTime = new \DateTime($dateTime);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Mit diesem Datum können wir leider nichts anfange. Bitte gib ein Datum im Format YYYY-MM-DD an.');
        }

        return $dateTime;
    }
    
    protected function getCheckedCitySlugRideDateRide($citySlug, $dateTime)
    {
        $city = $this->getCheckedCity($citySlug);
        $rideDateTime = $this->getCheckedDateTime($dateTime);
        $ride = $this->getCheckedRide($city, $rideDateTime);
        
        return $ride;
    }

    protected function getSession()
    {
        $session = new Session();

        return $session;
    }
}