<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\PhotoUploader;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassGalleryBundle\Entity\Photo;
use Caldera\CriticalmassGalleryBundle\Utility\ExifReader\DateTimeReader;
use Caldera\CriticalmassGalleryBundle\Utility\PhotoGps\PhotoGps;
use Caldera\CriticalmassGalleryBundle\Utility\PhotoResizer\PhotoResizer;

class PhotoUploader {
    protected $photo;
    protected $doctrine;
    protected $ride;
    protected $user;

    public function __construct(Photo $photo, $doctrine, Ride $ride, $user)
    {
        $this->photo = $photo;
        $this->doctrine = $doctrine;
        $this->ride = $ride;
        $this->user = $user;
    }

    public function execute()
    {
        $this->photo->getFile()->move($this->photo->getUploadRootDir(), $this->photo->getId().'.jpg');

        $this->makeSmallPhoto();
        $this->makeThumbnail();
        
        $this->readDateTimeFromExif();
        $this->computeGpsCoordinates();
    }
    
    protected function makeSmallPhoto()
    {
        $pr = new PhotoResizer();
        $pr->setPhoto($this->photo);
        $pr->resizeFactor(0.5);
        $pr->saveJpeg($this->photo->getUploadRootDir().$this->photo->getId().'.small.jpg');
    }
    
    protected function makeThumbnail()
    {
        $pr = new PhotoResizer();
        $pr->setPhoto($this->photo);
        $pr->resizeFactor(0.2);
        $pr->saveJpeg($this->photo->getUploadRootDir().$this->photo->getId().'.thumbnail.jpg');
    }
    
    protected function readDateTimeFromExif()
    {
        $dtr = new DateTimeReader($this->photo);
        $dtr->execute();

        $this->photo->setDateTime($dtr->getDateTime());
    }
    
    protected function computeGpsCoordinates()
    {
        $pg = new PhotoGps();
        $pg->setPhoto($this->photo);

        $track = $this->doctrine->getRepository('CalderaCriticalmassCoreBundle:Track')->findOneBy(array('ride' => $this->ride, 'user' => $this->user));
        $pg->setTrack($track);

        $pg->execute();
    }
}