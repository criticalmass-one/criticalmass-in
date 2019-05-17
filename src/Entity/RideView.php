<?php declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\ViewInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ride_view")
 * @ORM\Entity
 */
class RideView implements ViewInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Ride")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateTime;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user = null): ViewInterface
    {
        $this->user = $user;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): ViewInterface
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideView
    {
        $this->ride = $ride;

        return $this;
    }
}
