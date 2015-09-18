<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;
use Caldera\CriticalmassCoreBundle\Utility\LatLngArrayGenerator\SimpleLatLngArrayGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="track")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CriticalmassModelBundle\Repository\TrackRepository")
 */
class Track
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $username;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\Ride", inversedBy="tracks")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="tracks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\Ticket", inversedBy="tracks")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    protected $ticket;

    /**
     * @ORM\OneToOne(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate", mappedBy="track", cascade={"all"}, orphanRemoval=true)
     */
    protected $rideEstimate;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $endDateTime;

    /**
     * @ORM\Column(type="float")
     */
    protected $distance;

    /**
     * @ORM\Column(type="integer")
     */
    protected $points;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $md5Hash;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $activated;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $previewJsonArray;

    /**
     * This property contains the content of the gpx file, but will NOT be mapped to the database.
     *
     * @var
     */
    protected $gpx;

    public function __construct()
    {
        $this->setCreationDateTime(new \DateTime());

        if ($this->id) {
            $this->loadTrack();
        }
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
     * @param \Application\Sonata\UserBundle\Entity\User $user
     * @return Track
     */
    public function setUser(\Application\Sonata\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
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

    public function generateMD5Hash()
    {
        $this->setMd5Hash(md5($this->getGpx()));
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
        return $this->startDateTime->setTimezone(new \DateTimeZone('GMT'));
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
        return $this->endDateTime->setTimezone(new \DateTimeZone('GMT'));
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
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getDuration()
    {
        $diff = $this->endDateTime->diff($this->startDateTime);

        return $diff->format('%h.%i');
    }
    
    public function getAverageVelocity()
    {
        $diff = $this->endDateTime->diff($this->startDateTime);
        
        $averageVelocity = $this->getDistance() / $diff->format('%h');
        
        return $averageVelocity;
    }

    public function loadGpx($trackDirectory)
    {
        $this->gpx = file_get_contents($trackDirectory.$this->getId().'.gpx');
    }
    
    public function setGpx($gpx)
    {
        $this->gpx = $gpx;

        return $this;
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

    public function getGpx()
    {
        return $this->gpx;
    }

    public function loadTrack()
    {
        $this->setGpx(file_get_contents('/Users/maltehuebner/Documents/criticalmass.in/criticalmass/symfony/web/gpx/'.$this->getId().'.gpx'));
    }

    /**
     * Set previewJsonArray
     *
     * @param boolean $previewJsonArray
     * @return Track
     */
    public function setPreviewJsonArray($previewJsonArray)
    {
        $this->previewJsonArray = $previewJsonArray;

        return $this;
    }

    /**
     * Get previewJsonArray
     *
     * @return boolean 
     */
    public function getPreviewJsonArray()
    {
        return $this->previewJsonArray;
    }

    public function getColorRed()
    {
        return ($this->getUser() != null ? $this->getUser()->getColorRed() : $this->getTicket()->getColorRed());
    }

    public function getColorGreen()
    {
        return ($this->getUser() != null ? $this->getUser()->getColorGreen() : $this->getTicket()->getColorGreen());
    }

    public function getColorBlue()
    {
        return ($this->getUser() != null ? $this->getUser()->getColorBlue() : $this->getTicket()->getColorBlue());
    }
}
