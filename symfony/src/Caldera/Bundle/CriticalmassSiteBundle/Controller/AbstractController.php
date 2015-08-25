<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Repository\RideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
    protected function getCityBySlug($citySlug)
    {
        $citySlug = $this->getCitySlugRepository()->findOneBySlug($citySlug);

        if ($citySlug) {
            return $citySlug->getCity();
        } else {
            throw new NotFoundHttpException();
        }
    }
    
    protected function getBlogArticleRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Article');
    }

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

    protected function getCitySlugRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:CitySlug');
    }

    protected function getCityRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:City');
    }

    protected function getPhotoRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Photo');
    }

    protected function getPostRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Post');
    }

    protected function getTrackRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Track');
    }

    protected function getPositionRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Position');
    }

    protected function getSubrideRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCriticalmassModelBundle:Subride');
    }

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