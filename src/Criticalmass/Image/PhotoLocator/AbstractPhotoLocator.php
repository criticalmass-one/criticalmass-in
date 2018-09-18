<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoLocator;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Criticalmass\Image\PhotoGps\PhotoGps;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractPhotoLocator implements PhotoLocatorInterface
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var PhotoGps $photoGps */
    protected $photoGps;

    /** @var User $user */
    protected $user;

    /** @var Ride $ride */
    protected $ride;

    /** @var Track $track */
    protected $track;

    /** @var OutputInterface $output */
    protected $output;

    /** @var \DateTimeZone $dateTimeZone */
    protected $dateTimeZone;

    public function __construct(RegistryInterface $doctrine, PhotoGps $photoGps)
    {
        $this->doctrine = $doctrine;

        $this->photoGps = $photoGps;
    }

    public function setRide(Ride $ride): PhotoLocatorInterface
    {
        $this->ride = $ride;

        return $this;
    }

    public function setUser(User $user): PhotoLocatorInterface
    {
        $this->user = $user;

        return $this;
    }

    public function setTrack(Track $track = null): PhotoLocatorInterface
    {
        $this->track = $track;

        return $this;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone): PhotoLocatorInterface
    {
        $this->dateTimeZone = $dateTimeZone;

        return $this;
    }

    public function setOutput(OutputInterface $output): PhotoLocatorInterface
    {
        $this->output = $output;

        return $this;
    }
}
