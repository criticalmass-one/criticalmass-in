<?php declare(strict_types=1);

namespace App\Entity;

use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 * @ORM\Table(name="track_candidate")
 * @ORM\Entity(repositoryClass="App\Repository\TrackImportCandidateRepository")
 */
class TrackImportCandidate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Expose
     * @JMS\Type("int")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="trackImportCandidates")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     * @JMS\Type("Relation<App\Entity\User>")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ride", inversedBy="trackImportCandidates")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     * @JMS\Type("Relation<App\Entity\Ride>")
     */
    private ?Ride $ride = null;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose
     * @JMS\Type("int")
     */
    protected ?int $activityId = null;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected ?string $name = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Type("float")
     */
    protected ?float $distance = null;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     * @JMS\Type("int")
     */
    protected ?int $elapsedTime = null;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected ?string $type = null;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime")
     */
    protected ?\DateTime $startDateTime = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Type("float")
     */
    protected ?float $startLatitude = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Type("float")
     */
    protected ?float $startLongitude = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Type("float")
     */
    protected ?float $endLatitude = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Type("float")
     */
    protected ?float $endLongitude = null;

    /**
     * @ORM\Column(type="text")
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected ?string $polyline = null;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     * @JMS\Type("DateTime")
     */
    protected ?\DateTime $createdAt;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Type("bool")
     */
    protected bool $rejected = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TrackImportCandidate
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): TrackImportCandidate
    {
        $this->user = $user;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): TrackImportCandidate
    {
        $this->ride = $ride;

        return $this;
    }

    public function getActivityId(): int
    {
        return (int)$this->activityId;
    }

    public function setActivityId(int $activityId): TrackImportCandidate
    {
        $this->activityId = $activityId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TrackImportCandidate
    {
        $this->name = $name;

        return $this;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): TrackImportCandidate
    {
        $this->distance = $distance;

        return $this;
    }

    public function getElapsedTime(): int
    {
        return $this->elapsedTime;
    }

    public function setElapsedTime(int $elapsedTime): TrackImportCandidate
    {
        $this->elapsedTime = $elapsedTime;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): TrackImportCandidate
    {
        $this->type = $type;

        return $this;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTime $startDateTime): TrackImportCandidate
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getStartCoord(): CoordInterface
    {
        return new Coord($this->startLatitude, $this->startLongitude);
    }

    public function setStartCoord(CoordInterface $startCoord): TrackImportCandidate
    {
        $this->startLatitude = $startCoord->getLatitude();
        $this->startLongitude = $startCoord->getLongitude();

        return $this;
    }

    public function getStartLatitude(): float
    {
        return $this->startLatitude;
    }

    public function setStartLatitude(float $startLatitude): TrackImportCandidate
    {
        $this->startLatitude = $startLatitude;
        return $this;
    }

    public function getStartLongitude(): float
    {
        return $this->startLongitude;
    }

    public function setStartLongitude(float $startLongitude): TrackImportCandidate
    {
        $this->startLongitude = $startLongitude;

        return $this;
    }

    public function getEndCoord(): CoordInterface
    {
        return new Coord($this-$this->endLatitude, $this->endLongitude);
    }

    public function setEndCoord(CoordInterface $endCoord): TrackImportCandidate
    {
        $this->endLatitude = $endCoord->getLatitude();
        $this->endLongitude = $endCoord->getLongitude();

        return $this;
    }

    public function getEndLatitude(): float
    {
        return $this->endLatitude;
    }

    public function setEndLatitude(float $endLatitude): TrackImportCandidate
    {
        $this->endLatitude = $endLatitude;

        return $this;
    }

    public function getEndLongitude(): float
    {
        return $this->endLongitude;
    }

    public function setEndLongitude(float $endLongitude): TrackImportCandidate
    {
        $this->endLongitude = $endLongitude;

        return $this;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }

    public function setPolyline(string $polyline): TrackImportCandidate
    {
        $this->polyline = $polyline;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): TrackImportCandidate
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isRejected(): ?bool
    {
        return $this->rejected;
    }

    public function setRejected(bool $rejected): TrackImportCandidate
    {
        $this->rejected = $rejected;

        return $this;
    }
}
