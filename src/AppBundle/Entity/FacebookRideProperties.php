<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FacebookRidePropertiesRepository")
 * @ORM\Table(name="facebook_ride_properties")
 */
class FacebookRideProperties
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Ride")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberAttending;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberMaybe;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberDeclined;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberInterested;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numberNoreply;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $locationAddress;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name = null): FacebookRideProperties
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): FacebookRideProperties
    {
        $this->description = $description;

        return $this;
    }

    public function getNumberAttending(): ?int
    {
        return $this->numberAttending;
    }

    public function setNumberAttending(int $numberAttending = null): FacebookRideProperties
    {
        $this->numberAttending = $numberAttending;

        return $this;
    }

    public function getNumberMaybe(): ?int
    {
        return $this->numberMaybe;
    }

    public function setNumberMaybe(int $numberMaybe = null): FacebookRideProperties
    {
        $this->numberMaybe = $numberMaybe;

        return $this;
    }

    public function getNumberDeclined(): ?int
    {
        return $this->numberDeclined;
    }

    public function setNumberDeclined(int $numberDeclined = null): FacebookRideProperties
    {
        $this->numberDeclined = $numberDeclined;

        return $this;
    }

    public function getNumberInterested(): ?int
    {
        return $this->numberInterested;
    }

    public function setNumberInterested(int $numberInterested = null): FacebookRideProperties
    {
        $this->numberInterested = $numberInterested;

        return $this;
    }

    public function getNumberNoreply(): ?int
    {
        return $this->numberNoreply;
    }

    public function setNumberNoreply(int $numberNoreply = null): FacebookRideProperties
    {
        $this->numberNoreply = $numberNoreply;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime = null): FacebookRideProperties
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime = null): FacebookRideProperties
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getUpdatedTime(): ?\DateTime
    {
        return $this->updatedTime;
    }

    public function setUpdatedTime(\DateTime $updatedTime = null): FacebookRideProperties
    {
        $this->updatedTime = $updatedTime;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt = null): FacebookRideProperties
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null): FacebookRideProperties
    {
        $this->ride = $ride;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location = null): FacebookRideProperties
    {
        $this->location = $location;

        return $this;
    }

    public function getLocationAddress(): ?string
    {
        return $this->locationAddress;
    }

    public function setLocationAddress(string $locationAddress = null): FacebookRideProperties
    {
        $this->locationAddress = $locationAddress;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude = null): FacebookRideProperties
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude = null): FacebookRideProperties
    {
        $this->longitude = $longitude;

        return $this;
    }
}
