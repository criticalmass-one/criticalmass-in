<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockedCityRepository")
 * @ORM\Table(name="city_blocked")
 */
class BlockedCity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="blocked_cities", fetch="LAZY")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected ?City $city = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTime $blockStart = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTime $blockEnd = null;

    /**
     * @ORM\Column(type="text")
     */
    protected ?string $description = null;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $photosLink = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $rideListLink = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setBlockStart(\DateTime $blockStart): BlockedCity
    {
        $this->blockStart = $blockStart;

        return $this;
    }

    public function getBlockStart(): ?\DateTime
    {
        return $this->blockStart;
    }

    public function setBlockEnd(\DateTime $blockEnd): BlockedCity
    {
        $this->blockEnd = $blockEnd;

        return $this;
    }

    public function getBlockEnd(): ?\DateTime
    {
        return $this->blockEnd;
    }

    public function setDescription(string $description): BlockedCity
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setPhotosLink(bool $photosLink): BlockedCity
    {
        $this->photosLink = $photosLink;

        return $this;
    }

    public function getPhotosLink(): bool
    {
        return $this->photosLink;
    }

    public function setRideListLink(bool $rideListLink): BlockedCity
    {
        $this->rideListLink = $rideListLink;

        return $this;
    }

    public function getRideListLink(): bool
    {
        return $this->rideListLink;
    }

    public function setCity(City $city = null): BlockedCity
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }
}
