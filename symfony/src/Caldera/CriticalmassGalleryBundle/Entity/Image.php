<?php

namespace Caldera\CriticalmassGalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity
 */
class Image
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\Ride", inversedBy="images")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    private $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\User", inversedBy="images")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $exifMake;

    /**
     * @ORM\Column(type="string")
     */
    private $exifModel;

    /**
     * @ORM\Column(type="string")
     */
    private $exifLens;

    /**
     * @ORM\Column(type="float")
     */
    private $exifShutterSpeed;

    /**
     * @ORM\Column(type="float")
     */
    private $exifAperture;

    /**
     * @ORM\Column(type="datetime")
     */
    private $exifDateTime;

    /**
     * @ORM\Column(type="float")
     */
    private $exifFocalLength;

    /**
     * @ORM\Column(type="integer")
     */
    private $exifISO;

    /**
     * @ORM\Column(type="float")
     */
    private $exifLatitude;

    /**
     * @ORM\Column(type="float")
     */
    private $exifLongitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $exifFlash;

    /**
     * @ORM\Column(type="float")
     */
    private $exifExposureBias;

    /**
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @ORM\Column(type="integer")
     */
    private $fileSize;

    /**
     * @ORM\Column(type="integer")
     */
    private $visible;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDateTime;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set exifMake
     *
     * @param string $exifMake
     * @return Image
     */
    public function setExifMake($exifMake)
    {
        $this->exifMake = $exifMake;
    
        return $this;
    }

    /**
     * Get exifMake
     *
     * @return string 
     */
    public function getExifMake()
    {
        return $this->exifMake;
    }

    /**
     * Set exifModel
     *
     * @param string $exifModel
     * @return Image
     */
    public function setExifModel($exifModel)
    {
        $this->exifModel = $exifModel;
    
        return $this;
    }

    /**
     * Get exifModel
     *
     * @return string 
     */
    public function getExifModel()
    {
        return $this->exifModel;
    }

    /**
     * Set exifShutterSpeed
     *
     * @param float $exifShutterSpeed
     * @return Image
     */
    public function setExifShutterSpeed($exifShutterSpeed)
    {
        $this->exifShutterSpeed = $exifShutterSpeed;
    
        return $this;
    }

    /**
     * Get exifShutterSpeed
     *
     * @return float 
     */
    public function getExifShutterSpeed()
    {
        return $this->exifShutterSpeed;
    }

    /**
     * Set exifAperture
     *
     * @param float $exifAperture
     * @return Image
     */
    public function setExifAperture($exifAperture)
    {
        $this->exifAperture = $exifAperture;
    
        return $this;
    }

    /**
     * Get exifAperture
     *
     * @return float 
     */
    public function getExifAperture()
    {
        return $this->exifAperture;
    }

    /**
     * Set exifDateTime
     *
     * @param \DateTime $exifDateTime
     * @return Image
     */
    public function setExifDateTime($exifDateTime)
    {
        $this->exifDateTime = $exifDateTime;
    
        return $this;
    }

    /**
     * Get exifDateTime
     *
     * @return \DateTime 
     */
    public function getExifDateTime()
    {
        return $this->exifDateTime;
    }

    /**
     * Set exifFocalLength
     *
     * @param float $exifFocalLength
     * @return Image
     */
    public function setExifFocalLength($exifFocalLength)
    {
        $this->exifFocalLength = $exifFocalLength;
    
        return $this;
    }

    /**
     * Get exifFocalLength
     *
     * @return float 
     */
    public function getExifFocalLength()
    {
        return $this->exifFocalLength;
    }

    /**
     * Set exifISO
     *
     * @param integer $exifISO
     * @return Image
     */
    public function setExifISO($exifISO)
    {
        $this->exifISO = $exifISO;
    
        return $this;
    }

    /**
     * Get exifISO
     *
     * @return integer 
     */
    public function getExifISO()
    {
        return $this->exifISO;
    }

    /**
     * Set exifLatitude
     *
     * @param float $exifLatitude
     * @return Image
     */
    public function setExifLatitude($exifLatitude)
    {
        $this->exifLatitude = $exifLatitude;
    
        return $this;
    }

    /**
     * Get exifLatitude
     *
     * @return float 
     */
    public function getExifLatitude()
    {
        return $this->exifLatitude;
    }

    /**
     * Set exifLongitude
     *
     * @param float $exifLongitude
     * @return Image
     */
    public function setExifLongitude($exifLongitude)
    {
        $this->exifLongitude = $exifLongitude;
    
        return $this;
    }

    /**
     * Get exifLongitude
     *
     * @return float 
     */
    public function getExifLongitude()
    {
        return $this->exifLongitude;
    }

    /**
     * Set exifFlash
     *
     * @param integer $exifFlash
     * @return Image
     */
    public function setExifFlash($exifFlash)
    {
        $this->exifFlash = $exifFlash;
    
        return $this;
    }

    /**
     * Get exifFlash
     *
     * @return integer 
     */
    public function getExifFlash()
    {
        return $this->exifFlash;
    }

    /**
     * Set exifExposureBias
     *
     * @param float $exifExposureBias
     * @return Image
     */
    public function setExifExposureBias($exifExposureBias)
    {
        $this->exifExposureBias = $exifExposureBias;
    
        return $this;
    }

    /**
     * Get exifExposureBias
     *
     * @return float 
     */
    public function getExifExposureBias()
    {
        return $this->exifExposureBias;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;
    
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set fileSize
     *
     * @param integer $fileSize
     * @return Image
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    
        return $this;
    }

    /**
     * Get fileSize
     *
     * @return integer 
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set visible
     *
     * @param integer $visible
     * @return Image
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return integer 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Image
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;
    
        return $this;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime 
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * Set ride
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\Ride $ride
     * @return Image
     */
    public function setRide(\Caldera\CriticalmassCoreBundle\Entity\Ride $ride = null)
    {
        $this->ride = $ride;
    
        return $this;
    }

    /**
     * Get ride
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\Ride 
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Set user
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\User $user
     * @return Image
     */
    public function setUser(\Caldera\CriticalmassCoreBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set exifLens
     *
     * @param string $exifLens
     * @return Image
     */
    public function setExifLens($exifLens)
    {
        $this->exifLens = $exifLens;
    
        return $this;
    }

    /**
     * Get exifLens
     *
     * @return string 
     */
    public function getExifLens()
    {
        return $this->exifLens;
    }
}