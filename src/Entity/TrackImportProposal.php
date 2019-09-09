<?php declare(strict_types=1);

namespace App\Entity;

use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrackImportProposalRepository")
 */
class TrackImportProposal
{
    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User $user
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="trackImportProposals")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @var Ride $ride
     * @ORM\ManyToOne(targetEntity="App\Entity\Ride", inversedBy="trackImportProposals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ride;

    /**
     * @var int $activityId
     * @ORM\Column(type="bigint")
     */
    protected $activityId;

    /**
     * @var string $name
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var float $distance
     * @ORM\Column(type="float")
     */
    protected $distance;

    /**
     * @var int $elapsedTime
     * @ORM\Column(type="integer")
     */
    protected $elapsedTime;

    /**
     * @var string $type
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var \DateTime $startDateTime
     * @ORM\Column(type="datetime")
     */
    protected $startDateTime;

    /**
     * @var float $startLatitude
     * @ORM\Column(type="float")
     */
    protected $startLatitude;

    /**
     * @var float $startLongitude
     * @ORM\Column(type="float")
     */
    protected $startLongitude;

    /**
     * @var float $endLatitude
     * @ORM\Column(type="float")
     */
    protected $endLatitude;

    /**
     * @var float $endLongitude
     * @ORM\Column(type="float")
     */
    protected $endLongitude;

    /**
     * @var string $polyline
     * @ORM\Column(type="text")
     */
    protected $polyline;

    /**
     * @var \DateTime $createdAt
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TrackImportProposal
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): TrackImportProposal
    {
        $this->user = $user;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): TrackImportProposal
    {
        $this->ride = $ride;

        return $this;
    }

    public function getActivityId(): int
    {
        return (int)$this->activityId;
    }

    public function setActivityId(int $activityId): TrackImportProposal
    {
        $this->activityId = $activityId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TrackImportProposal
    {
        $this->name = $name;

        return $this;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): TrackImportProposal
    {
        $this->distance = $distance;

        return $this;
    }

    public function getElapsedTime(): int
    {
        return $this->elapsedTime;
    }

    public function setElapsedTime(int $elapsedTime): TrackImportProposal
    {
        $this->elapsedTime = $elapsedTime;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): TrackImportProposal
    {
        $this->type = $type;

        return $this;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTime $startDateTime): TrackImportProposal
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getStartCoord(): CoordInterface
    {
        return new Coord($this->startLatitude, $this->startLongitude);
    }

    public function setStartCoord(CoordInterface $startCoord): TrackImportProposal
    {
        $this->startLatitude = $startCoord->getLatitude();
        $this->startLongitude = $startCoord->getLongitude();

        return $this;
    }

    public function getStartLatitude(): float
    {
        return $this->startLatitude;
    }

    public function setStartLatitude(float $startLatitude): TrackImportProposal
    {
        $this->startLatitude = $startLatitude;
        return $this;
    }

    public function getStartLongitude(): float
    {
        return $this->startLongitude;
    }

    public function setStartLongitude(float $startLongitude): TrackImportProposal
    {
        $this->startLongitude = $startLongitude;

        return $this;
    }

    public function getEndCoord(): CoordInterface
    {
        return new Coord($this-$this->endLatitude, $this->endLongitude);
    }

    public function setEndCoord(CoordInterface $endCoord): TrackImportProposal
    {
        $this->endLatitude = $endCoord->getLatitude();
        $this->endLongitude = $endCoord->getLongitude();

        return $this;
    }

    public function getEndLatitude(): float
    {
        return $this->endLatitude;
    }

    public function setEndLatitude(float $endLatitude): TrackImportProposal
    {
        $this->endLatitude = $endLatitude;

        return $this;
    }

    public function getEndLongitude(): float
    {
        return $this->endLongitude;
    }

    public function setEndLongitude(float $endLongitude): TrackImportProposal
    {
        $this->endLongitude = $endLongitude;

        return $this;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }

    public function setPolyline(string $polyline): TrackImportProposal
    {
        $this->polyline = $polyline;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): TrackImportProposal
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
