<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\ParticipationRepository")
 * @ORM\Table(name="participation")
 */
class Participation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="participations")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="participations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $goingYes = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $goingMaybe = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $goingNo = true;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
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
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return Participation
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get goingYes
     *
     * @return boolean
     */
    public function getGoingYes()
    {
        return $this->goingYes;
    }

    /**
     * Set goingYes
     *
     * @param boolean $goingYes
     * @return Participation
     */
    public function setGoingYes($goingYes)
    {
        $this->goingYes = $goingYes;

        return $this;
    }

    /**
     * Get goingMaybe
     *
     * @return boolean
     */
    public function getGoingMaybe()
    {
        return $this->goingMaybe;
    }

    /**
     * Set goingMaybe
     *
     * @param boolean $goingMaybe
     * @return Participation
     */
    public function setGoingMaybe($goingMaybe)
    {
        $this->goingMaybe = $goingMaybe;

        return $this;
    }

    /**
     * Get goingNo
     *
     * @return boolean
     */
    public function getGoingNo()
    {
        return $this->goingNo;
    }

    /**
     * Set goingNo
     *
     * @param boolean $goingNo
     * @return Participation
     */
    public function setGoingNo($goingNo)
    {
        $this->goingNo = $goingNo;

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
     * Set ride
     *
     * @param Ride $ride
     * @return Participation
     */
    public function setRide(Ride $ride = null)
    {
        $this->ride = $ride;

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
     * Set user
     *
     * @param User $user
     * @return Participation
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }
}
