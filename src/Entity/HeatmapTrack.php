<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="heatmap_track")
 * @ORM\Entity(repositoryClass="App\Repository\HeatmapTrackRepository")
 */
class HeatmapTrack
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Heatmap", inversedBy="heatmapTracks")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Heatmap $heatmap = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Track", inversedBy="heatmapTracks")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Track $track = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeatmap(): ?Heatmap
    {
        return $this->heatmap;
    }

    public function setHeatmap(?Heatmap $heatmap): self
    {
        $this->heatmap = $heatmap;

        return $this;
    }

    public function getTrack(): ?Track
    {
        return $this->track;
    }

    public function setTrack(?Track $track): self
    {
        $this->track = $track;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
