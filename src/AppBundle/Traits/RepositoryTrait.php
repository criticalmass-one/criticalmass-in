<?php declare(strict_types=1);

namespace AppBundle\Traits;

use AppBundle\Repository\BikerightVoucherRepository;
use AppBundle\Repository\BlockedCityRepository;
use AppBundle\Repository\BoardRepository;
use AppBundle\Repository\CityCycleRepository;
use AppBundle\Repository\CityRepository;
use AppBundle\Repository\FrontpageTeaserRepository;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\ParticipationRepository;
use AppBundle\Repository\PhotoRepository;
use AppBundle\Repository\PostRepository;
use AppBundle\Repository\RegionRepository;
use AppBundle\Repository\RideEstimateRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\SubrideRepository;
use AppBundle\Repository\ThreadRepository;
use AppBundle\Repository\TrackRepository;
use AppBundle\Repository\WeatherRepository;
use Doctrine\Common\Persistence\ObjectRepository;

trait RepositoryTrait
{
    protected function getBikeRightVoucherRepository(): BikerightVoucherRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:BikerightVoucher');
    }

    protected function getBlockedCityRepository(): BlockedCityRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:BlockedCity');
    }

    protected function getBoardRepository(): BoardRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Board');
    }

    protected function getFrontpageTeaserRepository(): FrontpageTeaserRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:FrontpageTeaser');
    }

    protected function getRideRepository(): RideRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Ride');
    }

    protected function getCityCycleRepository(): CityCycleRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:CityCycle');
    }

    protected function getCitySlugRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:CitySlug');
    }

    protected function getCityRepository(): CityRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:City');
    }

    protected function getHelpCategoryRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:HelpCategory');
    }

    protected function getHelpItemRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:HelpItem');
    }

    protected function getLocationRepository(): LocationRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Location');
    }

    protected function getRegionRepository(): RegionRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Region');
    }

    protected function getPhotoRepository(): PhotoRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Photo');
    }

    protected function getPostRepository(): PostRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Post');
    }

    protected function getTrackRepository(): TrackRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Track');
    }

    protected function getThreadRepository(): ThreadRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Thread');
    }

    protected function getParticipationRepository(): ParticipationRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Participation');
    }

    protected function getSubrideRepository(): SubrideRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Subride');
    }

    protected function getWeatherRepository(): WeatherRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Weather');
    }

    protected function getRideEstimationRepository(): RideEstimateRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:RideEstimate');
    }
}
