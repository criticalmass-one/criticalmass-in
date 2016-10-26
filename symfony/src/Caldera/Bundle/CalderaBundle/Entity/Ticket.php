<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\TicketRepository")
 * @ORM\Table(name="glympse_ticket")
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="tickets")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="tickets")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\Column(type="string", length=9, nullable=false)
     */
    protected $inviteId;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $counter = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $colorRed = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $colorGreen = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $colorBlue = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $endDateTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $displayName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $message;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $exported = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $queried = false;

    public function __construct()
    {
        $this->creationDateTime = new \DateTime();
        $this->startDateTime = new \DateTime();
        $this->endDateTime = new \DateTime();
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
     * Get inviteId
     *
     * @return string
     */
    public function getInviteId()
    {
        return $this->inviteId;
    }

    /**
     * Set inviteId
     *
     * @param string $inviteId
     * @return Ticket
     */
    public function setInviteId($inviteId)
    {
        $this->inviteId = $inviteId;

        return $this;
    }

    public function getRide()
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Set counter
     *
     * @param integer $counter
     * @return Ticket
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

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
     * Set username
     *
     * @param string $username
     * @return Ticket
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get colorRed
     *
     * @return integer
     */
    public function getColorRed()
    {
        return $this->colorRed;
    }

    /**
     * Set colorRed
     *
     * @param integer $colorRed
     * @return Ticket
     */
    public function setColorRed($colorRed)
    {
        $this->colorRed = $colorRed;

        return $this;
    }

    /**
     * Get colorGreen
     *
     * @return integer
     */
    public function getColorGreen()
    {
        return $this->colorGreen;
    }

    /**
     * Set colorGreen
     *
     * @param integer $colorGreen
     * @return Ticket
     */
    public function setColorGreen($colorGreen)
    {
        $this->colorGreen = $colorGreen;

        return $this;
    }

    /**
     * Get colorBlue
     *
     * @return integer
     */
    public function getColorBlue()
    {
        return $this->colorBlue;
    }

    /**
     * Set colorBlue
     *
     * @param integer $colorBlue
     * @return Ticket
     */
    public function setColorBlue($colorBlue)
    {
        $this->colorBlue = $colorBlue;

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
     * Set startDateTime
     *
     * @param \DateTime $startDateTime
     * @return Ticket
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;

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
     * Set endDateTime
     *
     * @param \DateTime $endDateTime
     * @return Ticket
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Ticket
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Ticket
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function __toString()
    {
        return $this->getCity()->getCity() . ' ' . $this->getDisplayName();
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
     * Set city
     *
     * @param City $city
     * @return Ticket
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return Ticket
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

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
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Ticket
     */
    public function setCreationDateTime(\DateTime $creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    public function getExported()
    {
        return $this->exported;
    }

    public function setExported($exported)
    {
        $this->exported = $exported;

        return $this;
    }

    public function getQueried(): bool
    {
        return $this->queried;
    }

    public function setQueried(bool $queried)
    {
        $this->queried = $queried;

        return $this;
    }
}
