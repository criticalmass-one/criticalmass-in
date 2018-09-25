<?php declare(strict_types=1);

namespace App\Traits;

use App\Entity\BikerightVoucher;
use App\Entity\BlockedCity;
use App\Entity\Board;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\CitySlug;
use App\Entity\FrontpageTeaser;
use App\Entity\HelpCategory;
use App\Entity\HelpItem;
use App\Entity\Location;
use App\Entity\Participation;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Region;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\Subride;
use App\Entity\Thread;
use App\Entity\Track;
use App\Entity\Weather;
use App\Repository\BikerightVoucherRepository;
use App\Repository\BlockedCityRepository;
use App\Repository\BoardRepository;
use App\Repository\CityCycleRepository;
use App\Repository\CityRepository;
use App\Repository\FrontpageTeaserRepository;
use App\Repository\LocationRepository;
use App\Repository\ParticipationRepository;
use App\Repository\PhotoRepository;
use App\Repository\PostRepository;
use App\Repository\RegionRepository;
use App\Repository\RideEstimateRepository;
use App\Repository\RideRepository;
use App\Repository\SubrideRepository;
use App\Repository\ThreadRepository;
use App\Repository\TrackRepository;
use App\Repository\WeatherRepository;
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
