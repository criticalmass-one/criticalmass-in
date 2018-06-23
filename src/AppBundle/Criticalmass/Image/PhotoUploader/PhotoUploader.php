<?php

namespace AppBundle\Criticalmass\Image\PhotoUploader;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Entity\User;
use AppBundle\Criticalmass\Image\PhotoGps\PhotoGps;
use DirectoryIterator;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use PHPExif\Reader\Reader;

class PhotoUploader
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var string $uploadDestinationPhoto */
    protected $uploadDestinationPhoto;

    /** @var User $user */
    protected $user;

    /** @var Ride $ride */
    protected $ride;

    /** @var Track $track */
    protected $track;

    /** @var PhotoGps $photoGps */
    protected $photoGps;

    /** @var array $addedPhotoList */
    protected $addedPhotoList = [];

    public function __construct(Doctrine $doctrine, PhotoGps $photoGps, string $uploadDestinationPhoto)
    {
        $this->doctrine = $doctrine;
        $this->photoGps = $photoGps;
        $this->uploadDestinationPhoto = $uploadDestinationPhoto;
    }

    public function setUser(User $user): PhotoUploader
    {
        $this->user = $user;

        return $this;
    }

    public function setRide(Ride $ride): PhotoUploader
    {
        $this->ride = $ride;

        return $this;
    }

    public function setTrack(Track $track = null): PhotoUploader
    {
        $this->track = $track;

        return $this;
    }

    public function addFile(string $filename): PhotoUploader
    {
        $this->createPhotoEntity($filename);

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function addDirectory(string $directoryName): PhotoUploader
    {
        $dir = new DirectoryIterator($directoryName);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $this->addFile($fileinfo->getPathname());
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getAddedPhotoList(): array
    {
        return $this->addedPhotoList;
    }

    protected function calculateDateTime(Photo $photo): Photo
    {
        $photoFilename = sprintf('%s/%s', $this->uploadDestinationPhoto, $photo->getImageName());

        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->getExifFromFile($photoFilename);

        if ($dateTime = $exif->getCreationDate()) {
            $photo->setDateTime($dateTime);
        }

        return $photo;
    }

    protected function calculateLocation(Photo $photo): PhotoUploader
    {
        $this->photoGps
            ->setPhoto($photo)
            ->setTrack($this->track)
            ->execute();

        return $this;
    }

    protected function createPhotoEntity(string $sourceFilename): Photo
    {
        $photo = new Photo();

        $imageFilename = uniqid() . '.jpg';

        $destinationFilename = sprintf('%s/%s', $this->uploadDestinationPhoto, $imageFilename);

        copy($sourceFilename, $destinationFilename);

        $photo
            ->setImageName($imageFilename)
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setCity($this->ride->getCity());

        $this->calculateDateTime($photo);
        $this->calculateLocation($photo);

        $this->doctrine->getManager()->persist($photo);

        $this->addedPhotoList[] = $photo;

        return $photo;
    }
}
