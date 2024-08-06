<?php declare(strict_types=1);

namespace App\Traits;

use App\Entity\BlockedCity;
use App\Entity\Board;
use App\Entity\City;
use App\Entity\FrontpageTeaser;
use App\Entity\Location;
use App\Entity\Participation;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Region;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\Thread;
use App\Entity\Track;
use App\Entity\Weather;
use App\Repository\BlockedCityRepository;
use App\Repository\BoardRepository;
use App\Repository\CityRepository;
use App\Repository\FrontpageTeaserRepository;
use App\Repository\LocationRepository;
use App\Repository\ParticipationRepository;
use App\Repository\PhotoRepository;
use App\Repository\PostRepository;
use App\Repository\RegionRepository;
use App\Repository\RideRepository;
use App\Repository\SocialNetworkProfileRepository;
use App\Repository\SubrideRepository;
use App\Repository\ThreadRepository;
use App\Repository\TrackRepository;
use App\Repository\WeatherRepository;
use Doctrine\Common\Persistence\ObjectRepository;

/** @deprecated */
trait RepositoryTrait
{
    /** @deprecated */
    protected function getBlockedCityRepository(): BlockedCityRepository
    {
        return $this->getDoctrine()->getRepository(BlockedCity::class);
    }

    /** @deprecated */
    protected function getBoardRepository(): BoardRepository
    {
        return $this->getDoctrine()->getRepository(Board::class);
    }

    /** @deprecated */
    protected function getFrontpageTeaserRepository(): FrontpageTeaserRepository
    {
        return $this->getDoctrine()->getRepository(FrontpageTeaser::class);
    }

    /** @deprecated */
    protected function getRideRepository(): RideRepository
    {
        return $this->getDoctrine()->getRepository(Ride::class);
    }

    /** @deprecated */
    protected function getCityRepository(): CityRepository
    {
        return $this->getDoctrine()->getRepository(City::class);
    }

    /** @deprecated */
    protected function getLocationRepository(): LocationRepository
    {
        return $this->getDoctrine()->getRepository(Location::class);
    }

    /** @deprecated */
    protected function getRegionRepository(): RegionRepository
    {
        return $this->getDoctrine()->getRepository(Region::class);
    }

    /** @deprecated */
    protected function getPhotoRepository(): PhotoRepository
    {
        return $this->getDoctrine()->getRepository(Photo::class);
    }

    /** @deprecated */
    protected function getPostRepository(): PostRepository
    {
        return $this->getDoctrine()->getRepository(Post::class);
    }

    /** @deprecated */
    protected function getTrackRepository(): TrackRepository
    {
        return $this->getDoctrine()->getRepository(Track::class);
    }

    /** @deprecated */
    protected function getThreadRepository(): ThreadRepository
    {
        return $this->getDoctrine()->getRepository(Thread::class);
    }

    /** @deprecated */
    protected function getParticipationRepository(): ParticipationRepository
    {
        return $this->getDoctrine()->getRepository(Participation::class);
    }

    /** @deprecated */
    protected function getSocialNetworkProfileRepository(): SocialNetworkProfileRepository
    {
        return $this->getDoctrine()->getRepository(SocialNetworkProfile::class);
    }
}
