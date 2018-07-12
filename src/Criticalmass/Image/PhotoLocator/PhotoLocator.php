<?php

namespace AppBundle\Criticalmass\Image\PhotoLocator;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Entity\User;
use AppBundle\Criticalmass\Image\PhotoGps\PhotoGps;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Console\Output\OutputInterface;

class PhotoLocator
{
    /** @var Doctrine $doctrine */
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

    public function __construct(Doctrine $doctrine, PhotoGps $photoGps)
    {
        $this->doctrine = $doctrine;

        $this->photoGps = $photoGps;
    }

    public function setRide(Ride $ride): PhotoLocator
    {
        $this->ride = $ride;

        return $this;
    }

    public function setUser(User $user): PhotoLocator
    {
        $this->user = $user;

        return $this;
    }

    public function setTrack(Track $track = null): PhotoLocator
    {
        $this->track = $track;

        return $this;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone): PhotoLocator
    {
        $this->dateTimeZone = $dateTimeZone;

        return $this;
    }

    public function setOutput(OutputInterface $output): PhotoLocator
    {
        $this->output = $output;

        return $this;
    }

    public function relocate(): PhotoLocator
    {
        $photoList = $this->doctrine->getRepository(Photo::class)->findPhotosByRide($this->ride);
        /** @todo only lookup users photos */

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $this->photoGps
                ->setDateTimeZone($this->dateTimeZone)
                ->setPhoto($photo)
                ->setTrack($this->track);

            $this->photoGps->execute();

            $this->output->writeln(sprintf(
                'Updated location of photo <comment>#%d</comment> to <info>%f,%f</info>',
                $photo->getId(),
                $photo->getLatitude(),
                $photo->getLongitude()
            ));
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }
}
