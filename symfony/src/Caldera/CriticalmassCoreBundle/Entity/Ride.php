<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Stellt eine einzelne Tour einer Critical Mass dar.
 *
 * @ORM\Entity
 * @ORM\Table(name="ride")
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassCoreBundle\Entity\RideRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Ride
{
	/**
	 * Numerische ID der Tour.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

	/**
	 * Numerische ID der dazugehörigen Stadt, in der die Tour stattfindet.
	 *
	 * @ORM\ManyToOne(targetEntity="City", inversedBy="rides")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="Track", mappedBy="ride")
     */
    protected $tracks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * Startdatum und -uhrzeit der Tour.
     *
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

	/**
	 * Angabe, ob die Zeitangabe in den Tourinformationen dargestellt werden soll.
	 *
	 * @ORM\Column(type="boolean")
	 */
    protected $hasTime;

	/**
	 * Angabe, ob der Treffpunkt in den Tourinformationen dargestellt werden soll.
	 *
	 * @ORM\Column(type="boolean")
	 */
    protected $hasLocation;

	/**
	 * Bezeichnung des Treffpunktes der Tour als Zeichenkette.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
    protected $location;

	/**
	 * Breitengrad des Treffpunktes.
	 *
	 * @ORM\Column(type="float")
	 */
    protected $latitude;

	/**
	 * Laengengrad des Treffpunktes.
	 *
	 * @ORM\Column(type="float")
	 */
    protected $longitude;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $optimizedGpxContent;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $visibleSince;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $visibleUntil;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $expectedStartDateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enableTracking = true;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $estimatedParticipants;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $estimatedDistance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $estimatedDuration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

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
     * Set date
     *
     * @param \DateTime $dateTime
     * @return Ride
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set hasTime
     *
     * @param boolean $hasTime
     * @return Ride
     */
    public function setHasTime($hasTime)
    {
        $this->hasTime = $hasTime;
    
        return $this;
    }

    /**
     * Get hasTime
     *
     * @return boolean 
     */
    public function getHasTime()
    {
        return $this->hasTime;
    }

    /**
     * Set hasLocation
     *
     * @param boolean $hasLocation
     * @return Ride
     */
    public function setHasLocation($hasLocation)
    {
        $this->hasLocation = $hasLocation;
    
        return $this;
    }

    /**
     * Get hasLocation
     *
     * @return boolean 
     */
    public function getHasLocation()
    {
        return $this->hasLocation;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Ride
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set city
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\City $city
     * @return Ride
     */
    public function setCity(\Caldera\CriticalmassCoreBundle\Entity\City $city = null)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Ride
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Ride
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setOptimizedGpxContent($optimizedGpxContent)
    {
        $this->optimizedGpxContent = $optimizedGpxContent;
    }

    public function getOptimizedGpxContent()
    {
        return $this->optimizedGpxContent;
    }

    public function isEqual(Ride $ride)
    {
        return $ride->getId() == $this->getId();
    }

    public function getCityTitle()
    {
        return $this->getCity()->getTitle();
    }

    public function __toString()
    {
        if ($this->city)
        {
            return $this->city->getTitle()." - ".$this->getDateTime()->format("Y-m-d");
        }
        else
        {
            return $this->getDateTime()->format("Y-m-d");
        }
    }

    public function __construct()
    {
        $this->dateTime = new \DateTime();
        $this->latitude = 0.0;
        $this->longitude = 0.0;
    }

    /**
     * Set visibleSince
     *
     * @param \DateTime $visibleSince
     * @return Ride
     */
    public function setVisibleSince($visibleSince)
    {
        $this->visibleSince = $visibleSince;

        return $this;
    }

    /**
     * Get visibleSince
     *
     * @return \DateTime 
     */
    public function getVisibleSince()
    {
        return $this->visibleSince;
    }

    /**
     * Set visibleUntil
     *
     * @param \DateTime $visibleUntil
     * @return Ride
     */
    public function setVisibleUntil($visibleUntil)
    {
        $this->visibleUntil = $visibleUntil;

        return $this;
    }

    /**
     * Get visibleUntil
     *
     * @return \DateTime 
     */
    public function getVisibleUntil()
    {
        return $this->visibleUntil;
    }

    /**
     * Set expectedStartDateTime
     *
     * @param \DateTime $expectedStartDateTime
     * @return Ride
     */
    public function setExpectedStartDateTime($expectedStartDateTime)
    {
        $this->expectedStartDateTime = $expectedStartDateTime;

        return $this;
    }

    /**
     * Get expectedStartDateTime
     *
     * @return \DateTime 
     */
    public function getExpectedStartDateTime()
    {
        return $this->expectedStartDateTime;
    }

    /**
     * Set enableTracking
     *
     * @param boolean $enableTracking
     * @return Ride
     */
    public function setEnableTracking($enableTracking)
    {
        $this->enableTracking = $enableTracking;

        return $this;
    }

    /**
     * Get enableTracking
     *
     * @return boolean 
     */
    public function getEnableTracking()
    {
        return $this->enableTracking;
    }

    /**
     * Set estimatedParticipants
     *
     * @param integer $estimatedParticipants
     * @return Ride
     */
    public function setEstimatedParticipants($estimatedParticipants)
    {
        $this->estimatedParticipants = $estimatedParticipants;

        return $this;
    }

    /**
     * Get estimatedParticipants
     *
     * @return integer 
     */
    public function getEstimatedParticipants()
    {
        return $this->estimatedParticipants;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return Ride
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return Ride
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Ride
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getDate()
    {
        return $this->dateTime;
    }

    public function setDate(\DateTime $date)
    {
        $this->dateTime->add($this->dateTime->diff($date));
    }

    public function getTime()
    {
        return $this->dateTime;
    }

    public function setTime(\DateTime $time)
    {
        $this->dateTime->add($this->dateTime->diff($time));
    }

    /**
     * Set estimatedDistance
     *
     * @param float $estimatedDistance
     * @return Ride
     */
    public function setEstimatedDistance($estimatedDistance)
    {
        $this->estimatedDistance = $estimatedDistance;

        return $this;
    }

    /**
     * Get estimatedDistance
     *
     * @return float 
     */
    public function getEstimatedDistance()
    {
        return $this->estimatedDistance;
    }

    /**
     * Set estimatedDuration
     *
     * @param float $estimatedDuration
     * @return Ride
     */
    public function setEstimatedDuration($estimatedDuration)
    {
        $this->estimatedDuration = $estimatedDuration;

        return $this;
    }

    /**
     * Get estimatedDuration
     *
     * @return float 
     */
    public function getEstimatedDuration()
    {
        return $this->estimatedDuration;
    }

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues

        $basePath = '/Applications/XAMPP/htdocs/criticalmass/symfony/web/images/ride/';
        $path = $basePath.$this->getCity()->getMainSlugString().'/';

        //@mkdir($path, 0777, true);
        // move takes the target directory and target filename as params
        $this->getFile()->move($path, $path.$this->getId().'.jpg');

        // set the path property to the filename where you've saved the file
        $this->filename = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload() {
        $this->upload();
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated() {
        $this->setUpdated(date('Y-m-d H:i:s'));
    }

    /**
     * Add tracks
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\Track $tracks
     * @return Ride
     */
    public function addTrack(\Caldera\CriticalmassCoreBundle\Entity\Track $tracks)
    {
        $this->tracks[] = $tracks;

        return $this;
    }

    /**
     * Remove tracks
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\Track $tracks
     */
    public function removeTrack(\Caldera\CriticalmassCoreBundle\Entity\Track $tracks)
    {
        $this->tracks->removeElement($tracks);
    }

    /**
     * Get tracks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTracks()
    {
        return $this->tracks;
    }
}
