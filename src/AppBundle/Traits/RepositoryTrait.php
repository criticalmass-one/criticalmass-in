<?php declare(strict_types=1);

namespace AppBundle\Traits;

use AppBundle\Entity\BikerightVoucher;
use AppBundle\Entity\BlockedCity;
use AppBundle\Entity\Board;
use AppBundle\Entity\City;
use AppBundle\Entity\CityCycle;
use AppBundle\Entity\CitySlug;
use AppBundle\Entity\FrontpageTeaser;
use AppBundle\Entity\HelpCategory;
use AppBundle\Entity\HelpItem;
use AppBundle\Entity\Location;
use AppBundle\Entity\Participation;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Post;
use AppBundle\Entity\Region;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Entity\Subride;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Track;
use AppBundle\Entity\Weather;
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
        return $this->getDoctrine()->getRepository(BikerightVoucher::class);
    }

    protected function getBlockedCityRepository(): BlockedCityRepository
    {
        return $this->getDoctrine()->getRepository(BlockedCity::class);
    }

    protected function getBoardRepository(): BoardRepository
    {
        return $this->getDoctrine()->getRepository(Board::class);
    }

    protected function getFrontpageTeaserRepository(): FrontpageTeaserRepository
    {
        return $this->getDoctrine()->getRepository(FrontpageTeaser::class);
    }

    protected function getRideRepository(): RideRepository
    {
        return $this->getDoctrine()->getRepository(Ride::class);
    }

    protected function getCityCycleRepository(): CityCycleRepository
    {
        return $this->getDoctrine()->getRepository(CityCycle::class);
    }

    protected function getCitySlugRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository(CitySlug::class);
    }

    protected function getCityRepository(): CityRepository
    {
        return $this->getDoctrine()->getRepository(City::class);
    }

    protected function getHelpCategoryRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository(HelpCategory::class);
    }

    protected function getHelpItemRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository(HelpItem::class);
    }

    protected function getLocationRepository(): LocationRepository
    {
        return $this->getDoctrine()->getRepository(Location::class);
    }

    protected function getRegionRepository(): RegionRepository
    {
        return $this->getDoctrine()->getRepository(Region::class);
    }

    protected function getPhotoRepository(): PhotoRepository
    {
        return $this->getDoctrine()->getRepository(Photo::class);
    }

    protected function getPostRepository(): PostRepository
    {
        return $this->getDoctrine()->getRepository(Post::class);
    }

    protected function getTrackRepository(): TrackRepository
    {
        return $this->getDoctrine()->getRepository(Track::class);
    }

    protected function getThreadRepository(): ThreadRepository
    {
        return $this->getDoctrine()->getRepository(Thread::class);
    }

    protected function getParticipationRepository(): ParticipationRepository
    {
        return $this->getDoctrine()->getRepository(Participation::class);
    }

    protected function getSubrideRepository(): SubrideRepository
    {
        return $this->getDoctrine()->getRepository(Subride::class);
    }

    protected function getWeatherRepository(): WeatherRepository
    {
        return $this->getDoctrine()->getRepository(Weather::class);
    }

    protected function getRideEstimationRepository(): RideEstimateRepository
    {
        return $this->getDoctrine()->getRepository(RideEstimate::class);
    }
}
