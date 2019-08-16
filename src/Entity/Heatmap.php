<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Heatmap\HeatmapInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="heatmap")
 * @ORM\Entity(repositoryClass="App\Repository\HeatmapRepository")
 */
class Heatmap implements HeatmapInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identifier;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="heatmap", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Ride", inversedBy="heatmap", cascade={"persist"})
     */
    private $ride;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\City", inversedBy="heatmap", cascade={"persist"})
     */
    private $city;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Track", inversedBy="heatmaps")
     */
    private $tracks;

    public function __construct()
    {
        $this->tracks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(?Ride $ride): self
    {
        $this->ride = $ride;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Track[]
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
        }

        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->contains($track)) {
            $this->tracks->removeElement($track);
        }

        return $this;
    }
}
