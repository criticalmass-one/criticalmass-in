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
    protected $controller;

    public function __construct($controller, Photo $photo, Ride $ride)
    {
        $this->controller = $controller;
        $this->photo = $photo;
        $this->doctrine = $controller->getDoctrine();
        $this->ride = $ride;
        $this->user = $controller->getUser();
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
    
    public function computeGpsCoordinates()
    {
        $pg = new PhotoGps($this->controller);
        $pg->setPhoto($this->photo);

        $track = $this->doctrine->getRepository('CalderaCriticalmassTrackBundle:Track')->findOneBy(array('ride' => $this->ride, 'user' => $this->user, 'activated' => true));
        $pg->setTrack($track);

        $pg->execute();
    }
}