<?php declare(strict_types=1);

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'city_blocked')]
#[ORM\Entity(repositoryClass: 'App\Repository\BlockedCityRepository')]
class BlockedCity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'blocked_cities', fetch: 'LAZY')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    protected ?City $city = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?Carbon $blockStart = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?Carbon $blockEnd = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $photosLink = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $rideListLink = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setBlockStart(Carbon $blockStart): BlockedCity
    {
        $this->blockStart = $blockStart;

        return $this;
    }

    public function getBlockStart(): ?Carbon
    {
        return $this->blockStart;
    }

    public function setBlockEnd(Carbon $blockEnd): BlockedCity
    {
        $this->blockEnd = $blockEnd;

        return $this;
    }

    public function getBlockEnd(): ?Carbon
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

    public function setCity(?City $city = null): BlockedCity
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }
}
