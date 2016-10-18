<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\ViewInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ride_view")
 * @ORM\Entity()
 */
class RideView implements ViewInterface
{
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
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getRide()
    {
        return $this->ride;
    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;

        return $this;
    }
}
