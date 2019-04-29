<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Criticalmass\Image\PhotoGps\PhotoGps;
use League\Flysystem\FilesystemInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractPhotoUploader implements PhotoUploaderInterface
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var User $user */
    protected $user;

    /** @var Ride $ride */
    protected $ride;

    /** @var Track $track */
    protected $track;

    /** @var PhotoGps $photoGps */
    protected $photoGps;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /** @var array $addedPhotoList */
    protected $addedPhotoList = [];

    public function __construct(RegistryInterface $doctrine, PhotoGps $photoGps, EventDispatcherInterface $eventDispatcher, FilesystemInterface $filesystem)
    {
        $this->doctrine = $doctrine;
        $this->photoGps = $photoGps;
        $this->filesystem = $filesystem;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setUser(User $user): PhotoUploaderInterface
    {
        $this->user = $user;

        return $this;
    }

    public function setRide(Ride $ride): PhotoUploaderInterface
    {
        $this->ride = $ride;

        return $this;
    }

    public function setTrack(Track $track = null): PhotoUploaderInterface
    {
        $this->track = $track;

        return $this;
    }
}
