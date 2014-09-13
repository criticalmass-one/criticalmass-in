<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
     * @ORM\Column(type="string", length=32)
     */
    protected $md5Hash;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    protected $gpx;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    protected $json;

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
     * Set gpx
     *
     * @param string $gpx
     * @return Track
     */
    public function setGpx($gpx)
    {
        $this->gpx = $gpx;

        return $this;
    }

    /**
     * Get gpx
     *
     * @return string 
     */
    public function getGpx()
    {
        return $this->gpx;
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
     * Set json
     *
     * @param string $json
     * @return Track
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json
     *
     * @return string 
     */
    public function getJson()
    {
        return $this->json;
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
}
