<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Caldera\Bundle\CriticalmassModelBundle\EntityInterface\ViewInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ride_view")
 * @ORM\Entity()
 */
class RideView implements ViewInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ride_views")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="ride_views")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;


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
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide()
    {
        return $this->ride;
    }
}
