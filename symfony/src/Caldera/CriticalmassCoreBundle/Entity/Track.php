<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="track")
 * @ORM\Entity()
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
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="tracks")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="tracks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassGlympseBundle\Entity\Ticket", inversedBy="tracks")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    protected $ticket;

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
     * @ORM\Column(type="integer")
     */
    protected $timeStamps;

    /**
     * @ORM\OneToOne(targetEntity="Caldera\CriticalmassStatisticBundle\Entity\RideEstimate", mappedBy="track", cascade={"all"}, orphanRemoval=true)
     */
    protected $rideEstimate;

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

        if ($this->id)
        {
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
     * @param \Caldera\CriticalmassCoreBundle\Entity\Ride $ride
     * @return Track
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
     * @param \Caldera\CriticalmassGlympseBundle\Entity\Ticket $ticket
     * @return Track
     */
    public function setTicket(\Caldera\CriticalmassGlympseBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \Caldera\CriticalmassGlympseBundle\Entity\Ticket 
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

        if ($this->getCreationDateTime())
        {
            $result .= $this->getCreationDateTime()->format('Y-m-d');
        }

        if ($this->getRide())
        {
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
        return $this->startDateTime;
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
        return $this->endDateTime;
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
    public function getTimeStamps()
    {
        return $this->timeStamps;
    }

    /**
     * @param mixed $timeStamps
     */
    public function setTimeStamps($timeStamps)
    {
        $this->timeStamps = $timeStamps;
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

    public function handleUpload()
    {
        $gpxReader = new GpxReader();

        if ($gpxReader->loadFile($this->file->getPathname()))
        {
            $this->setStartDateTime($gpxReader->getStartDateTime());
            $this->setEndDateTime($gpxReader->getEndDateTime());
            $this->setPoints($gpxReader->countPoints());
            $this->setMd5Hash($gpxReader->getMd5Hash());
            $this->setGpx($gpxReader->getFileContent());
            //$this->setJson($gpxReader->generateJson());
            $this->setDistance($gpxReader->calculateDistance());
            $this->setActivated(1);

            $this->timeStamps = 0;

            for ($i = 0; $i < $this->points; $i++) {
                if ($gpxReader->getTimeOfPoint($i) != "") {
                    $this->timeStamps++;
                }
            }

            return true;
        }

        return false;
    }

    public function getDuration()
    {
        $diff = $this->endDateTime->diff($this->startDateTime);

        return $diff->format('%h.%i');
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
     * @param \Caldera\CriticalmassStatisticBundle\Entity\RideEstimate $rideEstimate
     * @return Track
     */
    public function setRideEstimate(\Caldera\CriticalmassStatisticBundle\Entity\RideEstimate $rideEstimate = null)
    {
        $this->rideEstimate = $rideEstimate;

        return $this;
    }

    /**
     * Get rideEstimate
     *
     * @return \Caldera\CriticalmassStatisticBundle\Entity\RideEstimate 
     */
    public function getRideEstimate()
    {
        return $this->rideEstimate;
    }

    public function setGpx($gpx)
    {
        $this->gpx = $gpx;
    }

    public function getGpx()
    {
        return $this->gpx;
    }

    public function saveTrack()
    {
        if (!$handle = fopen('/Users/maltehuebner/Documents/criticalmass.in/criticalmass/symfony/web/gpx/'.$this->getId().'.gpx', "a")) {
            print "Kann die Datei nicht öffnen";
            exit;
        }

        // Schreibe $somecontent in die geöffnete Datei.
        if (!fwrite($handle, $this->getGpx())) {
            print "Kann in die Datei nicht schreiben";
            exit;
        }

        print "Fertig, in Datei wurde geschrieben";

        fclose($handle);
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
}
