<?php

namespace Caldera\CriticalmassGalleryBundle\Utility;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassCoreBundle\Entity\User;
use Caldera\CriticalmassGalleryBundle\Entity\Image;

class ImageImporter
{
    private $imageDirectory;
    private $imageFileArray = array();
    private $doctrine;
    private $ride;
    private $user;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function setDirectory($directory)
    {
        $this->imageDirectory = new ImageDirectory($directory);

        return $this;
    }

    public function fetchImageFiles()
    {
        $this->imageFileArray = $this->imageDirectory->getImageFileArray();

        return $this;
    }

    public function execute()
    {
        foreach ($this->imageFileArray as $imageFilename)
        {
            $image = new Image();
            $iew = new ImageExifWriter($image);
            $iew->readExifFromFilename($imageFilename);

            $image->setTitle($imageFilename);
            $image->setRide($this->ride);
            $image->setUser($this->user);
            $image->setVisible(true);
            $image->setCreationDateTime(new \DateTime());

            $em = $this->doctrine->getManager();
            $em->persist($image);
            $em->flush();

            copy("/Applications/XAMPP/htdocs/criticalmass/symfony/web/".$imageFilename, "/Applications/XAMPP/htdocs/criticalmass/symfony/web/images/".$image->getId().".jpg");
        }
    }
} 