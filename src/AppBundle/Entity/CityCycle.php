<?php

namespace AppBundle\Entity;

use AppBundle\EntityInterface\BoardInterface;
use AppBundle\EntityInterface\ElasticSearchPinInterface;
use AppBundle\EntityInterface\PhotoInterface;
use AppBundle\EntityInterface\RouteableInterface;
use AppBundle\EntityInterface\ViewableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @ORM\Table(name="city_cycle")
 */
class CityCycle
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="cityCycles")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cityCycles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    protected $dayOfWeek;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $weekOfMonth;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $time;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude = 0;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $validFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $validUntil;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCity(City $city): CityCycle
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setUser(User $user): CityCycle
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setLatitude(float $latitude): CityCycle
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): CityCycle
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setDayOfWeek(int $dayOfWeek): CityCycle
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setWeekOfMonth(int $weekOfMonth): CityCycle
    {
        $this->weekOfMonth = $weekOfMonth;

        return $this;
    }

    public function getWeekOfMonth(): ?int
    {
        return $this->weekOfMonth;
    }

    public function setTime(\DateTime $time): CityCycle
    {
        $this->time = $time;

        return $this;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setLocation(string $location = null): CityCycle
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setCreatedAt(\DateTime $createdAt): CityCycle
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): CityCycle
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setValidFrom(\DateTime $validFrom = null): CityCycle
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidFrom(): \DateTime
    {
        return $this->validFrom;
    }

    public function setValidUntil(\DateTime $validUntil = null): CityCycle
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }
}
