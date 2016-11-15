<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\ArchiveableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ParticipateableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\EventRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Event implements ParticipateableInterface, ViewableInterface, ArchiveableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="events")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @JMS\Expose
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
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
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="archive_events")
     * @ORM\JoinColumn(name="archive_parent_id", referencedColumnName="id")
     */
    protected $archiveParent;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isArchived = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $archiveDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="archive_events")
     * @ORM\JoinColumn(name="archive_user_id", referencedColumnName="id")
     */
    protected $archiveUser;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $archiveMessage;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="event")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="event")
     */
    protected $photos;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
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
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $views = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Event
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
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return Event
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
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

    /**
     * Set hasTime
     *
     * @param boolean $hasTime
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * Set latitude
     *
     * @param float $latitude
     * @return Event
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
     * @return Event
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

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return Event
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
     * @return Event
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
     * Set url
     *
     * @param string $url
     * @return Event
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return Event
     */
    public function setIsArchived(bool $isArchived)
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
     * @return Event
     */
    public function setArchiveDateTime(\DateTime $archiveDateTime)
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Event
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set participationsNumberYes
     *
     * @param integer $participationsNumberYes
     * @return Event
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
     * @return Event
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
     * @return Event
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

    /**
     * Set user
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\User $user
     * @return Event
     */
    public function setUser(\Caldera\Bundle\CalderaBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set city
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\City $city
     * @return Event
     */
    public function setCity(\Caldera\Bundle\CalderaBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set archiveParent
     *
     * @param ArchiveableInterface $archiveParent
     * @return Event
     */
    public function setArchiveParent(ArchiveableInterface $archiveParent = null)
    {
        $this->archiveParent = $archiveParent;

        return $this;
    }

    /**
     * Get archiveParent
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\Event
     */
    public function getArchiveParent()
    {
        return $this->archiveParent;
    }

    /**
     * Set archiveUser
     *
     * @param User $archiveUser
     * @return Event
     */
    public function setArchiveUser(User $archiveUser)
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    /**
     * Get archiveUser
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\User
     */
    public function getArchiveUser()
    {
        return $this->archiveUser;
    }

    public function setArchiveMessage($archiveMessage)
    {
        $this->archiveMessage = $archiveMessage;

        return $this;
    }

    public function getArchiveMessage()
    {
        return $this->archiveMessage;
    }
    
    /**
     * Add posts
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Post $posts
     * @return Event
     */
    public function addPost(\Caldera\Bundle\CalderaBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Post $posts
     */
    public function removePost(\Caldera\Bundle\CalderaBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add photos
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Photo $photos
     * @return Event
     */
    public function addPhoto(\Caldera\Bundle\CalderaBundle\Entity\Photo $photos)
    {
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Photo $photos
     */
    public function removePhoto(\Caldera\Bundle\CalderaBundle\Entity\Photo $photos)
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
     * Set slug
     *
     * @param string $slug
     * @return Event
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
}
