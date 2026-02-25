<?php declare(strict_types=1);

namespace App\Entity;

use App\Enum\PolylineResolution;
use App\Repository\TrackPolylineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Table(name: 'track_polyline')]
#[ORM\UniqueConstraint(name: 'track_resolution_unique', fields: ['track', 'resolution'])]
#[ORM\Entity(repositoryClass: TrackPolylineRepository::class)]
class TrackPolyline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['timelapse', 'api-public'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Track::class, inversedBy: 'trackPolylines')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Ignore]
    private ?Track $track = null;

    #[ORM\Column(type: 'smallint')]
    #[Groups(['timelapse', 'api-public'])]
    private ?int $resolution = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['timelapse', 'api-public'])]
    private ?string $polyline = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['timelapse', 'api-public'])]
    private ?int $numPoints = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Ignore]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrack(): ?Track
    {
        return $this->track;
    }

    public function setTrack(Track $track): static
    {
        $this->track = $track;

        return $this;
    }

    public function getResolution(): ?PolylineResolution
    {
        return $this->resolution !== null ? PolylineResolution::from($this->resolution) : null;
    }

    public function setResolution(PolylineResolution $resolution): static
    {
        $this->resolution = $resolution->value;

        return $this;
    }

    public function getPolyline(): ?string
    {
        return $this->polyline;
    }

    public function setPolyline(string $polyline): static
    {
        $this->polyline = $polyline;

        return $this;
    }

    public function getNumPoints(): ?int
    {
        return $this->numPoints;
    }

    public function setNumPoints(int $numPoints): static
    {
        $this->numPoints = $numPoints;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
