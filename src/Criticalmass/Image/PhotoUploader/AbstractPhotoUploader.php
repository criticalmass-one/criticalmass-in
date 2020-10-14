<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use League\Flysystem\FilesystemInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractPhotoUploader implements PhotoUploaderInterface
{
    /** @var ManagerRegistry $doctrine */
    protected $doctrine;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var User $user */
    protected $user;

    /** @var Ride $ride */
    protected $ride;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /** @var array $addedPhotoList */
    protected $addedPhotoList = [];

    /** @var UploadFakerInterface $uploadFaker */
    protected $uploadFaker;

    public function __construct(ManagerRegistry $doctrine, EventDispatcherInterface $eventDispatcher, FilesystemInterface $filesystem, UploadFakerInterface $uploadFaker)
    {
        $this->doctrine = $doctrine;
        $this->filesystem = $filesystem;
        $this->eventDispatcher = $eventDispatcher;
        $this->uploadFaker = $uploadFaker;
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

    /** @deprecated  */
    public function setTrack(Track $track = null): PhotoUploaderInterface
    {
        return $this;
    }
}
