<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoLocator;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Symfony\Component\Console\Output\OutputInterface;

interface PhotoLocatorInterface
{
    public function setRide(Ride $ride): PhotoLocatorInterface;
    public function setUser(User $user): PhotoLocatorInterface;
    public function setTrack(Track $track = null): PhotoLocatorInterface;
    public function setDateTimeZone(\DateTimeZone $dateTimeZone): PhotoLocatorInterface;
    public function setOutput(OutputInterface $output): PhotoLocatorInterface;
    public function relocate(): PhotoLocatorInterface;
}
