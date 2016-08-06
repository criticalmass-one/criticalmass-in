<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\ParticipateableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="ride")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\RideRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Ride implements ParticipateableInterface, ViewableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @JMS\Expose
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="Track", mappedBy="ride", fetch="LAZY")
     */
    protected $tracks;

    /**
     * @ORM\OneToMany(targetEntity="Subride", mappedBy="ride", fetch="LAZY")
     */
    protected $subrides;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $dateTime;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     */
    protected $hasTime;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     */
    protected $hasLocation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $location;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     */
    protected $longitude;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Expose
     */
    protected $estimatedParticipants;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $estimatedDistance;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $estimatedDuration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     */
    protected $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     */
    protected $url;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="archiveRides", fetch="LAZY")
     * @ORM\JoinColumn(name="archive_parent_id", referencedColumnName="id")
     */
    protected $archiveParent;

    /**
     * @ORM\OneToMany(targetEntity="Ride", mappedBy="archiveParent", fetch="LAZY")
     */
    protected $archiveRides;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isArchived = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $archiveDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="archive_rides", fetch="LAZY")
     * @ORM\JoinColumn(name="archive_user_id", referencedColumnName="id")
     */
    protected $archiveUser;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="ride", fetch="LAZY")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="ride", fetch="LAZY")
     */
    protected $photos;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $participationsNumberYes = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $participationsNumberMaybe = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $participationsNumberNo = 0;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="ride", fetch="LAZY")
     */
    protected $participations;

    /**
     * @ORM\OneToMany(targetEntity="RideEstimate", mappedBy="ride", fetch="LAZY")
     */
    protected $estimates;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="featuredRides", fetch="LAZY")
     * @ORM\JoinColumn(name="featured_photo", referencedColumnName="id")
     */
    protected $featuredPhoto;

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
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
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
     * @param City $city
     * @return Ride
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
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

    public function isEqual(Ride $ride)
    {
        return $ride->getId() == $this->getId();
    }

    public function equals(Ride $ride)
    {
        return $this->isEqual($ride);
    }

    public function isSameRide(Ride $ride)
    {
        return $ride->getCity()->getId() == $this->getCity()->getId() && $ride->getFormattedDate() == $this->getFormattedDate();
    }

    public function __toString()
    {
        if ($this->city) {
            return $this->city->getTitle() . " - " . $this->getDateTime()->format("Y-m-d");
        } else {
            return $this->getDateTime()->format("Y-m-d");
        }
    }

    public function __construct()
    {
        $this->dateTime = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->visibleSince = new \DateTime();
        $this->visibleUntil = new \DateTime();
        $this->expectedStartDateTime = new \DateTime();
        $this->archiveDateTime = new \DateTime();
        $this->latitude = 0.0;
        $this->longitude = 0.0;
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

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("timestamp")
     * @JMS\Type("integer")
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->dateTime->format('U');
    }



    public function getFormattedDate()
    {
        return $this->dateTime->format('Y-m-d');
    }

    public function getDate()
    {
        return $this->dateTime;
    }

    public function getTime()
    {
        return $this->dateTime;
    }

    public function setDate(\DateTime $date)
    {
        $this->dateTime = new \DateTime($date->format('Y-m-d') . ' ' . $this->dateTime->format('H:i:s'), $date->getTimezone());
    }

    public function setTime(\DateTime $time)
    {
        $this->dateTime = new \DateTime($this->dateTime->format('Y-m-d') . ' ' . $time->format('H:i:s'), $time->getTimezone());
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
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated()
    {
        $this->setUpdated(date('Y-m-d H:i:s'));
    }

    /**
     * Add tracks
     *
     * @param Track $tracks
     * @return Ride
     */
    public function addTrack(Track $tracks)
    {
        $this->tracks[] = $tracks;

        return $this;
    }

    /**
     * Remove tracks
     *
     * @param Track $tracks
     */
    public function removeTrack(Track $tracks)
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

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("title")
     * @JMS\Type("string")
     * @return string
     */
    public function getFancyTitle()
    {
        if (!$this->title)
        {
            return $this->city->getTitle().' '.$this->dateTime->format('d.m.Y');
        }

        return $this->getTitle();
    }

    public function __clone()
    {
        $this->id = null;
        $this->setIsArchived(true);
        $this->setArchiveDateTime(new \DateTime());
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return Ride
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean 
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set archiveDateTime
     *
     * @param \DateTime $archiveDateTime
     * @return Ride
     */
    public function setArchiveDateTime($archiveDateTime)
    {
        $this->archiveDateTime = $archiveDateTime;

        return $this;
    }

    /**
     * Get archiveDateTime
     *
     * @return \DateTime 
     */
    public function getArchiveDateTime()
    {
        return $this->archiveDateTime;
    }

    /**
     * Set archiveUser
     *
     * @param User $archiveUser
     * @return Ride
     */
    public function setArchiveUser(User $archiveUser = null)
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    /**
     * Get archiveUser
     *
     * @return User
     */
    public function getArchiveUser()
    {
        return $this->archiveUser;
    }

    /**
     * Set archiveParent
     *
     * @param $archiveParent
     * @return Ride
     */
    public function setArchiveParent(Ride $archiveParent = null)
    {
        $this->archiveParent = $archiveParent;

        return $this;
    }

    /**
     * Get archiveParent
     *
     * @return Ride
     */
    public function getArchiveParent()
    {
        return $this->archiveParent;
    }

    /**
     * Add posts
     *
     * @param Post $posts
     * @return City
     */
    public function addPost(Post $posts)
    {
        $this->posts[] = $posts;
    }
    
    /**
     * Add subrides
     *
     * @param Subride $subrides
     * @return Ride
     */
    public function addSubride(Subride $subrides)
    {
        $this->subrides[] = $subrides;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param Post $posts
     */
    public function removePost(Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add photos
     *
     * @param Photo $photos
     * @return Ride
     */
    public function addPhoto(Photo $photos)
    {
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos
     *
     * @param Photo $photos
     */
    public function removePhoto(Photo $photos)
    {
        $this->photos->removeElement($photos);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }
    
    /**
     * Remove subrides
     *
     * @param Subride $subrides
     */
    public function removeSubride(Subride $subrides)
    {
        $this->subrides->removeElement($subrides);
    }

    /**
     * Get subrides
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubrides()
    {
        return $this->subrides;
    }

    public function getDurationInterval()
    {
        $totalMinutes = $this->estimatedDuration * 60.0;

        $hours = floor($totalMinutes / 60.0);
        $minutes = $totalMinutes % 60.0;

        return new \DateInterval('PT'.$hours.'H'.$minutes.'M');
    }
    
    public function getAverageVelocity()
    {
        if (!$this->getEstimatedDuration() || !$this->getEstimatedDistance()) {
            return 0;
        }
        
        return $this->getEstimatedDistance() / $this->getEstimatedDuration();
    }

    public function getPin()
    {
        return $this->latitude.','.$this->longitude;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set participationsNumberYes
     *
     * @param integer $participationsNumberYes
     * @return Ride
     */
    public function setParticipationsNumberYes($participationsNumberYes)
    {
        $this->participationsNumberYes = $participationsNumberYes;

        return $this;
    }

    /**
     * Get participationsNumberYes
     *
     * @return integer 
     */
    public function getParticipationsNumberYes()
    {
        return $this->participationsNumberYes;
    }

    /**
     * Set participationsNumberMaybe
     *
     * @param integer $participationsNumberMaybe
     * @return Ride
     */
    public function setParticipationsNumberMaybe($participationsNumberMaybe)
    {
        $this->participationsNumberMaybe = $participationsNumberMaybe;

        return $this;
    }

    /**
     * Get participationsNumberMaybe
     *
     * @return integer 
     */
    public function getParticipationsNumberMaybe()
    {
        return $this->participationsNumberMaybe;
    }

    /**
     * Set participationsNumberNo
     *
     * @param integer $participationsNumberNo
     * @return Ride
     */
    public function setParticipationsNumberNo($participationsNumberNo)
    {
        $this->participationsNumberNo = $participationsNumberNo;

        return $this;
    }

    /**
     * Get participationsNumberNo
     *
     * @return integer 
     */
    public function getParticipationsNumberNo()
    {
        return $this->participationsNumberNo;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function incViews()
    {
        ++$this->views;
    }

    public function getCountry()
    {
        if ($this->city) {
            return $this->city->getCountry();
        }

        return null;
    }

    public function getIsEnabled()
    {
        if ($this->city) {
            return $this->city->isEnabled();
        }

        return null;
    }

    public function setFeaturedPhoto(Photo $featuredPhoto)
    {
        $this->featuredPhoto = $featuredPhoto;
    }

    public function getFeaturedPhoto()
    {
        return $this->featuredPhoto;
    }
}
