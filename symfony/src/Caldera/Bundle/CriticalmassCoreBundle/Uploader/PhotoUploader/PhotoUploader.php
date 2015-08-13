<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Uploader\PhotoUploader;

use Caldera\Bundle\CriticalmassCoreBundle\Gallery\ExifReader\DateTimeReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gallery\PhotoGps\PhotoGps;
use Caldera\Bundle\CriticalmassCoreBundle\Gallery\PhotoResizer\PhotoResizer;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;

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

        $track = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Track')->findOneBy(array('ride' => $this->ride, 'user' => $this->user, 'activated' => true));
        $pg->setTrack($track);

        $pg->execute();
    }
}