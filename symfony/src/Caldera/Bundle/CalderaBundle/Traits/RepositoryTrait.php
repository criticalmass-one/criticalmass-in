<?php

namespace Caldera\Bundle\CalderaBundle\Traits;

use Caldera\Bundle\CalderaBundle\Repository\AnonymousNameRepository;
use Caldera\Bundle\CalderaBundle\Repository\BlogPostRepository;
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

trait RepositoryTrait
{
    /**
     * @return AnonymousNameRepository
     */
    protected function getAnonymousNameRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:AnonymousName');
    }

    /**
     * @return ObjectRepository
     */
    protected function getBlogRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Blog');
    }

    /**
     * @return BlogPostRepository
     */
    protected function getBlogPostRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:BlogPost');
    }

    /**
     * @return BoardRepository
     */
    protected function getBoardRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Board');
    }

    /**
     * @return ContentRepository
     */
    protected function getContentRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Content');
    }

    /**
     * @return EventRepository
     */
    protected function getEventRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Event');
    }

    /**
     * @return RideRepository
     */
    protected function getRideRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Ride');
    }

    /**
     * @return ObjectRepository
     */
    protected function getCitySlugRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:CitySlug');
    }

    /**
     * @return CityRepository
     */
    protected function getCityRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:City');
    }

    /**
     * @return ObjectRepository
     */
    protected function getFacebookCityPropertiesRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:FacebookCityProperties');
    }

    /**
     * @return FacebookRidePropertiesRepository
     */
    protected function getFacebookRidePropertiesRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:FacebookRideProperties');
    }

    /**
     * @return IncidentRepository
     */
    protected function getIncidentRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Incident');
    }

    /**
     * @return LocationRepository
     */
    protected function getLocationRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Location');
    }

    /**
     * @return LocationRepository
     */
    protected function getNotificationSubscriptionRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:NotificationSubscription');
    }

    /**
     * @return RegionRepository
     */
    protected function getRegionRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Region');
    }

    /**
     * @return PhotoRepository
     */
    protected function getPhotoRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Photo');
    }

    /**
     * @return PostRepository
     */
    protected function getPostRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Post');
    }

    /**
     * @return TrackRepository
     */
    protected function getTrackRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Track');
    }

    /**
     * @return ThreadRepository
     */
    protected function getThreadRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Thread');
    }

    /**
     * @return ParticipationRepository
     */
    protected function getParticipationRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Participation');
    }

    /**
     * @return ObjectRepository
     */
    protected function getPositionRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Position');
    }

    /**
     * @return SubrideRepository
     */
    protected function getSubrideRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Subride');
    }

    /**
     * @return ObjectRepository
     */
    protected function getGlympseTicketRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Ticket');
    }

    /**
     * @return WeatherRepository
     */
    protected function getWeatherRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaBundle:Weather');
    }
}