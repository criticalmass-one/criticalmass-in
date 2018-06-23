<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Traits;

use Criticalmass\Bundle\AppBundle\Repository\BikerightVoucherRepository;
use Criticalmass\Bundle\AppBundle\Repository\BlockedCityRepository;
use Criticalmass\Bundle\AppBundle\Repository\BoardRepository;
use Criticalmass\Bundle\AppBundle\Repository\CityCycleRepository;
use Criticalmass\Bundle\AppBundle\Repository\CityRepository;
use Criticalmass\Bundle\AppBundle\Repository\FrontpageTeaserRepository;
use Criticalmass\Bundle\AppBundle\Repository\LocationRepository;
use Criticalmass\Bundle\AppBundle\Repository\ParticipationRepository;
use Criticalmass\Bundle\AppBundle\Repository\PhotoRepository;
use Criticalmass\Bundle\AppBundle\Repository\PostRepository;
use Criticalmass\Bundle\AppBundle\Repository\RegionRepository;
use Criticalmass\Bundle\AppBundle\Repository\RideEstimateRepository;
use Criticalmass\Bundle\AppBundle\Repository\RideRepository;
use Criticalmass\Bundle\AppBundle\Repository\SubrideRepository;
use Criticalmass\Bundle\AppBundle\Repository\ThreadRepository;
use Criticalmass\Bundle\AppBundle\Repository\TrackRepository;
use Criticalmass\Bundle\AppBundle\Repository\WeatherRepository;
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
