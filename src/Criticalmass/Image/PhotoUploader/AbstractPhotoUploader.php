<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractPhotoUploader implements PhotoUploaderInterface
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var string $uploadDestinationPhoto */
    protected $uploadDestinationPhoto;

    /** @var User $user */
    protected $user;

    /** @var Ride $ride */
    protected $ride;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /** @var array $addedPhotoList */
    protected $addedPhotoList = [];

    public function __construct(RegistryInterface $doctrine, string $uploadDestinationPhoto, EventDispatcherInterface $eventDispatcher)
    {
        $this->doctrine = $doctrine;
        $this->uploadDestinationPhoto = $uploadDestinationPhoto;
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

    /** @deprecated  */
    public function setTrack(Track $track = null): PhotoUploaderInterface
    {
        return $this;
    }
}
