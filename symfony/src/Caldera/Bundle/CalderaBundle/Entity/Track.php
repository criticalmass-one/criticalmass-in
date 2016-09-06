<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="track")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\TrackRepository")
 * @Vich\Uploadable
 * @JMS\ExclusionPolicy("all")
 */
class Track
{
    const TRACK_SOURCE_GPX = 'TRACK_SOURCE_GPX';
    const TRACK_SOURCE_STRAVA = 'TRACK_SOURCE_STRAVA';
    const TRACK_SOURCE_RUNKEEPER = 'TRACK_SOURCE_RUNKEEPER';
    const TRACK_SOURCE_RUNTASTIC = 'TRACK_SOURCE_RUNTASTIC';
    const TRACK_SOURCE_DRAW = 'TRACK_SOURCE_DRAW';
    const TRACK_SOURCE_GLYMPSE = 'TRACK_SOURCE_GLYMPSE';
    const TRACK_SOURCE_CRITICALMAPS = 'TRACK_SOURCE_CRITICALMAPS';
    const TRACK_SOURCE_UNKNOWN = 'TRACK_SOURCE_UNKNOWN';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
	 */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $username;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="tracks")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tracks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="tracks")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    protected $ticket;

    /**
     * @ORM\ManyToOne(targetEntity="CriticalmapsUser", inversedBy="tracks")
     * @ORM\JoinColumn(name="criticalmapsuser_id", referencedColumnName="id")
     */
    protected $criticalmapsUser;

    /**
     * @ORM\OneToOne(targetEntity="RideEstimate", mappedBy="track", cascade={"all"}, orphanRemoval=true)
     */
    protected $rideEstimate;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $endDateTime;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $distance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $points;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $startPoint;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $endPoint;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $md5Hash;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $activated = true;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @deprecated
     */
    protected $latLngList;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $geoJson;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @JMS\SerializedName("polylineString")
     */
    protected $polyline;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="track_file", fileNameProperty="trackFilename")
     * @var File
     */
    protected $trackFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $trackFilename;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('TRACK_SOURCE_GPX', 'TRACK_SOURCE_STRAVA', 'TRACK_SOURCE_RUNKEEPER', 'TRACK_SOURCE_RUNTASTIC', 'TRACK_SOURCE_DRAW', 'TRACK_SOURCE_GLYMPSE', 'TRACK_SOURCE_CRITICALMAPS', 'TRACK_SOURCE_UNKNOWN')")
     */
    protected $source = self::TRACK_SOURCE_UNKNOWN;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    protected $stravaActitityId;

    public function __construct()
    {
        $this->setCreationDateTime(new \DateTime());

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
     * Set username
     *
     * @param string $username
     * @return Track
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set ride
     *
     * @param Ride $ride
     * @return Track
     */
    public function setRide(Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return Ride
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Track
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ticket
     *
     * @param Ticket $ticket
     * @return Track
     */
    public function setTicket(Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    public function setCriticalmapsUser(CriticalmapsUser $criticalmapsUser = null)
    {
        $this->criticalmapsUser = $criticalmapsUser;

        return $this;
    }

    public function getCriticalmapsUser()
    {
        return $this->criticalmapsUser;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Track
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
     * Set md5Hash
     *
     * @param string $md5Hash
     * @return Track
     */
    public function setMd5Hash($md5Hash)
    {
        $this->md5Hash = $md5Hash;

        return $this;
    }

    /**
     * Get md5Hash
     *
     * @return string 
     */
    public function getMd5Hash()
    {
        return $this->md5Hash;
    }

    /**
     * Set startDateTime
     *
     * @param \DateTime $startDateTime
     * @return Track
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    /**
     * Get startDateTime
     *
     * @return \DateTime 
     */
    public function getStartDateTime()
    {
        if ($this->startDateTime) {
            return $this->startDateTime->setTimezone(new \DateTimeZone('UTC'));
        }
    }

    /**
     * Set endDateTime
     *
     * @param \DateTime $endDateTime
     * @return Track
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    /**
     * Get endDateTime
     *
     * @return \DateTime 
     */
    public function getEndDateTime()
    {
        if ($this->endDateTime) {
            return $this->endDateTime->setTimezone(new \DateTimeZone('UTC'));
        }
    }

    /**
     * Set distance
     *
     * @param float $distance
     * @return Track
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return Track
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }
    
    /**
     * @return mixed
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * @param mixed $activated
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;
    }
    
    /**
     * Set rideEstimate
     *
     * @param RideEstimate $rideEstimate
     * @return Track
     */
    public function setRideEstimate(RideEstimate $rideEstimate = null)
    {
        $this->rideEstimate = $rideEstimate;

        return $this;
    }

    /**
     * Get rideEstimate
     *
     * @return RideEstimate
     */
    public function getRideEstimate()
    {
        return $this->rideEstimate;
    }

    /**
     * Set latLngList
     *
     * @param string $latLngList
     * @return Track
     */
    public function setLatLngList($latLngList)
    {
        $this->latLngList = $latLngList;

        return $this;
    }

    /**
     * Get latLngList
     *
     * @return array
     */
    public function getLatLngList()
    {
        return $this->latLngList;
    }

    public function setPolyline($polyline)
    {
        $this->polyline = $polyline;

        return $this;
    }

    public function getPolyline()
    {
        return $this->polyline;
    }

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("colorRed")
     */
    public function getColorRed()
    {
        if ($this->getUser()) {
            return $this->getUser()->getColorRed();
        } elseif ($this->getTicket()) {
            return $this->getTicket()->getColorRed();
        } elseif ($this->getCriticalmapsUser()) {
            return $this->getCriticalmapsUser()->getColorRed();
        }

        return null;
    }

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("colorGreen")
     */
    public function getColorGreen()
    {
        if ($this->getUser()) {
            return $this->getUser()->getColorGreen();
        } elseif ($this->getTicket()) {
            return $this->getTicket()->getColorGreen();
        } elseif ($this->getCriticalmapsUser()) {
            return $this->getCriticalmapsUser()->getColorGreen();
        }

        return null;
    }

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("colorBlue")
     */
    public function getColorBlue()
    {
        if ($this->getUser()) {
            return $this->getUser()->getColorBlue();
        } elseif ($this->getTicket()) {
            return $this->getTicket()->getColorBlue();
        } elseif ($this->getCriticalmapsUser()) {
            return $this->getCriticalmapsUser()->getColorBlue();
        }

        return null;
    }

    public function __toString()
    {
        $result = $this->getUsername().'(';

        if ($this->getCreationDateTime()) {
            $result .= $this->getCreationDateTime()->format('Y-m-d');
        }

        if ($this->getRide()) {
            $result .= ', '.$this->getRide()->getCity()->getCity();
        }

        $result .= ')';

        return $result;
    }
    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $track
     */
    public function setTrackFile(File $track = null)
    {
        $this->trackFile = $track;

        if ($track) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getTrackFile()
    {
        return $this->trackFile;
    }

    /**
     * @param string $trackFilename
     */
    public function setTrackFilename($trackFilename)
    {
        $this->trackFilename = $trackFilename;
    }

    /**
     * @return string
     */
    public function getTrackFilename()
    {
        return $this->trackFilename;
    }

    /**
     * Set startPoint
     *
     * @param integer $startPoint
     * @return Track
     */
    public function setStartPoint($startPoint)
    {
        if ($startPoint >= 1) {
            $this->startPoint = $startPoint;
        } else {
            $this->startPoint = 1;
        }

        return $this;
    }

    /**
     * Get startPoint
     *
     * @return integer 
     */
    public function getStartPoint()
    {
        return $this->startPoint;
    }

    /**
     * Set endPoint
     *
     * @param integer $endPoint
     * @return Track
     */
    public function setEndPoint($endPoint)
    {
        if ($endPoint <= $this->points) {
            $this->endPoint = $endPoint;
        } else {
            $this->endPoint = $this->points - 1;
        }

        return $this;
    }

    /**
     * Get endPoint
     *
     * @return integer 
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Track
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    public function getDurationInterval()
    {
        if ($this->startDateTime and $this->endDateTime) {
            return $this->getEndDateTime()->diff($this->getStartDateTime());
        }

        return null;
    }
    
    public function getDurationInSeconds()
    {
        if ($this->startDateTime and $this->endDateTime) {
            return $this->getEndDateTime()->getTimestamp() - $this->getStartDateTime()->getTimestamp();
        }

        return 0;
    }
    
    public function getAverageVelocity()
    {
        if ($this->startDateTime and $this->endDateTime and $this->distance) {
            $kilometres = $this->getDistance();
            $seconds = $this->getEndDateTime()->getTimestamp() - $this->getStartDateTime()->getTimestamp();

            $hours = (float)$seconds / 3600;

            $velocity = $kilometres / ($hours + 0.0001);

            return $velocity;
        }

        return null;
    }

    public function getStartTime()
    {
        return $this->startDateTime;
    }

    public function setStartTime(\DateTime $time)
    {
        $this->startDateTime = new \DateTime($this->startDateTime->format('Y-m-d') . ' ' . $time->format('H:i:s'));
    }

    public function getStartDate()
    {
        return $this->startDateTime;
    }

    public function setStartDate(\DateTime $date)
    {
        $newDate = new \DateTime($this->startDateTime->format('Y-m-d') . ' 00:00:00');

        $this->startDateTime = $newDate->add($newDate->diff($date));
    }

    public function setSource(string $source)
    {
        $this->source = $source;

        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setStravaActivityId(int $stravaActivityId)
    {
        $this->stravaActitityId = $stravaActivityId;
    }

    public function getStravaActivityId()
    {
        return $this->stravaActitityId;
    }

    public function setGeoJson(string $geoJson): Track
    {
        $this->geoJson = $geoJson;

        return $this;
    }

    public function getWaypointList(): string
    {
        return $this->geoJson;
    }
}
