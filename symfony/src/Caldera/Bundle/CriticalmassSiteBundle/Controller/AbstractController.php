<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Repository\CityRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\ContentRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\PhotoRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\PostRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\RideRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\SubrideRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\ThreadRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\TrackRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractController extends Controller
{
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
     * @return ObjectRepository
     */
    protected function getBlogArticleRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Article');
    }

    /**
     * @return ContentRepository
     */
    protected function getContentRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Content');
    }

    /**
     * @return RideRepository
     */
    protected function getRideRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Ride');
    }

    /**
     * @return ObjectRepository
     */
    protected function getCitySlugRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:CitySlug');
    }

    /**
     * @return CityRepository
     */
    protected function getCityRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:City');
    }
    
    /**
     * @return PhotoRepository
     */
    protected function getPhotoRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Photo');
    }

    /**
     * @return PostRepository
     */
    protected function getPostRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Post');
    }

    /**
     * @return TrackRepository
     */
    protected function getTrackRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Track');
    }

    /**
     * @return ThreadRepository
     */
    protected function getThreadRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Thread');
    }

    /**
     * @return ObjectRepository
     */
    protected function getPositionRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Position');
    }

    /**
     * @return SubrideRepository
     */
    protected function getSubrideRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Subride');
    }

    /**
     * @return ObjectRepository
     */
    protected function getGlympseTicketRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Ticket');
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
}