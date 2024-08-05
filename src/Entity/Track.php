<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Geo\Entity\Track as GeoTrack;
use App\Criticalmass\Geo\EntityInterface\TrackInterface;
use MalteHuebner\OrderedEntitiesBundle\Annotation as OE;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;
use App\Criticalmass\Router\Attribute as Routing;
use App\Criticalmass\UploadableDataHandler\UploadableEntity;
use App\Criticalmass\UploadFaker\FakeUploadable;
use App\EntityInterface\RouteableInterface;
use App\EntityInterface\StaticMapableInterface;
use Caldera\GeoBasic\Track\TrackInterface as BaseTrackInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 * @OE\OrderedEntity()
 */
#[Routing\DefaultRoute(name: 'caldera_criticalmass_track_view')]
#[ORM\Table(name: 'track')]
#[ORM\Entity(repositoryClass: 'App\Repository\TrackRepository')]
#[JMS\ExclusionPolicy('all')]
#[ORM\Index(fields: ['creationDateTime'], name: 'track_creation_date_time_index')]
class Track extends GeoTrack implements RouteableInterface, StaticMapableInterface, TrackInterface, UploadableEntity, FakeUploadable, OrderedEntityInterface
{
    const TRACK_SOURCE_GPX = 'TRACK_SOURCE_GPX';
    const TRACK_SOURCE_STRAVA = 'TRACK_SOURCE_STRAVA';
    const TRACK_SOURCE_RUNTASTIC = 'TRACK_SOURCE_RUNTASTIC';
    const TRACK_SOURCE_DRAW = 'TRACK_SOURCE_DRAW';
    const TRACK_SOURCE_GLYMPSE = 'TRACK_SOURCE_GLYMPSE';
    const TRACK_SOURCE_CRITICALMAPS = 'TRACK_SOURCE_CRITICALMAPS';
    const TRACK_SOURCE_UNKNOWN = 'TRACK_SOURCE_UNKNOWN';

    #[Routing\RouteParameter(name: 'trackId')]
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[JMS\Groups(['timelapse', 'api-private'])]
    #[JMS\Expose]
    protected ?string $username = null;

    #[ORM\ManyToOne(targetEntity: 'Ride', inversedBy: 'tracks')]
    #[ORM\JoinColumn(name: 'ride_id', referencedColumnName: 'id')]
    protected ?Ride $ride = null;

    /**
     * @OE\Identical()
     */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'tracks')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[JMS\Groups(['timelapse', 'api-private'])]
    #[JMS\Expose]
    protected ?User $user = null;

    #[ORM\OneToOne(targetEntity: 'RideEstimate', mappedBy: 'track', cascade: ['all'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'estimate_id', referencedColumnName: 'id')]
    protected ?RideEstimate $rideEstimate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?\DateTime $creationDateTime = null;

    /**
     * @OE\Order(direction="asc")
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?\DateTime $startDateTime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?\DateTime $endDateTime = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?float $distance = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?int $points = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?int $startPoint = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    protected ?int $endPoint = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    protected ?string $md5Hash = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $enabled = true;

    /**
     * @OE\Boolean(value=false)
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $deleted = false;

    /**
     * @deprecated
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $latLngList = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $geoJson = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    #[JMS\SerializedName('polylineString')]
    protected ?string $polyline = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[JMS\Groups(['timelapse', 'api-public'])]
    #[JMS\Expose]
    #[JMS\SerializedName('reducedPolylineString')]
    protected ?string $reducedPolyline = null;

    /**
     * @Vich\UploadableField(mapping="track_file", fileNameProperty="trackFilename",  size="trackSize", mimeType="trackMimeType")
     */
    protected ?File $trackFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $trackFilename = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $trackSize = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $trackMimeType = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $updatedAt = null;

    /**
     * $source must be nullable du to legacy tracks without source attribution
     */
    #[ORM\Column(type: 'string', nullable: true, columnDefinition: "ENUM('TRACK_SOURCE_GPX', 'TRACK_SOURCE_STRAVA', 'TRACK_SOURCE_RUNKEEPER', 'TRACK_SOURCE_RUNTASTIC', 'TRACK_SOURCE_DRAW', 'TRACK_SOURCE_GLYMPSE', 'TRACK_SOURCE_CRITICALMAPS', 'TRACK_SOURCE_UNKNOWN')")]
    protected ?string $source = self::TRACK_SOURCE_UNKNOWN;

    #[ORM\Column(type: 'bigint', nullable: true)]
    protected ?int $stravaActitityId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $reviewed = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUsername(string $username): Track
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setRide(Ride $ride = null): Track
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setUser(User $user = null): Track
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setMd5Hash(string $md5Hash): Track
    {
        $this->md5Hash = $md5Hash;

        return $this;
    }

    public function getMd5Hash(): ?string
    {
        return $this->md5Hash;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): Track
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): Track
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function setRideEstimate(RideEstimate $rideEstimate = null): Track
    {
        $this->rideEstimate = $rideEstimate;

        return $this;
    }

    public function getRideEstimate(): ?RideEstimate
    {
        return $this->rideEstimate;
    }

    public function setLatLngList(string $latLngList): Track
    {
        $this->latLngList = $latLngList;

        return $this;
    }

    public function getLatLngList(): string
    {
        return $this->latLngList;
    }

    public function setPolyline(string $polyline): BaseTrackInterface
    {
        $this->polyline = $polyline;

        return $this;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }

    public function setReducedPolyline(string $reducedPolyline = null): TrackInterface
    {
        $this->reducedPolyline = $reducedPolyline;

        return $this;
    }

    public function getReducedPolyline(): ?string
    {
        return $this->reducedPolyline;
    }

    #[JMS\Groups(['timelapse'])]
    #[JMS\VirtualProperty]
    #[JMS\SerializedName('colorRed')]
    public function getColorRed(): ?int
    {
        if ($this->getUser()) {
            return $this->getUser()->getColorRed();
        } elseif ($this->getTicket()) {
            return $this->getTicket()->getColorRed();
        } elseif ($this->getCriticalmapsUser()) {
            return $this->getCriticalmapsUser()->getColorRed();
        }

        return null;
    }

    #[JMS\Groups(['timelapse'])]
    #[JMS\VirtualProperty]
    #[JMS\SerializedName('colorGreen')]
    public function getColorGreen(): ?int
    {
        if ($this->getUser()) {
            return $this->getUser()->getColorGreen();
        } elseif ($this->getTicket()) {
            return $this->getTicket()->getColorGreen();
        } elseif ($this->getCriticalmapsUser()) {
            return $this->getCriticalmapsUser()->getColorGreen();
        }

        return null;
    }

    #[JMS\Groups(['timelapse'])]
    #[JMS\VirtualProperty]
    #[JMS\SerializedName('colorBlue')]
    public function getColorBlue(): ?int
    {
        if ($this->getUser()) {
            return $this->getUser()->getColorBlue();
        } elseif ($this->getTicket()) {
            return $this->getTicket()->getColorBlue();
        } elseif ($this->getCriticalmapsUser()) {
            return $this->getCriticalmapsUser()->getColorBlue();
        }

        return null;
    }

    public function __toString(): string
    {
        $result = $this->getUsername() . '(';

        if ($this->getCreationDateTime()) {
            $result .= $this->getCreationDateTime()->format('Y-m-d');
        }

        if ($this->getRide()) {
            $result .= ', ' . $this->getRide()->getCity()->getCity();
        }

        $result .= ')';

        return $result;
    }

    public function getTrackSize(): ?int
    {
        return $this->trackSize;
    }

    public function setTrackSize(int $trackSize = null): Track
    {
        $this->trackSize = $trackSize;

        return $this;
    }

    public function getTrackMimeType(): ?string
    {
        return $this->trackMimeType;
    }

    public function setTrackMimeType(string $trackMimeType = null): Track
    {
        $this->trackMimeType = $trackMimeType;

        return $this;
    }

    /** @deprecated  */
    public function getDurationInterval(): ?\DateInterval
    {
        if ($this->startDateTime && $this->endDateTime) {
            return $this->getEndDateTime()->diff($this->getStartDateTime());
        }

        return null;
    }

    public function getDurationInSeconds(): int
    {
        if ($this->startDateTime && $this->endDateTime) {
            return $this->getEndDateTime()->getTimestamp() - $this->getStartDateTime()->getTimestamp();
        }

        return 0;
    }

    public function getAverageVelocity(): ?float
    {
        if ($this->startDateTime && $this->endDateTime && $this->distance) {
            $kilometres = $this->getDistance();
            $seconds = $this->getEndDateTime()->getTimestamp() - $this->getStartDateTime()->getTimestamp();

            $hours = (float)$seconds / 3600;

            $velocity = $kilometres / ($hours + 0.0001);

            return $velocity;
        }

        return null;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startDateTime;
    }

    public function setStartTime(\DateTime $time): Track
    {
        $this->startDateTime = new \DateTime($this->startDateTime->format('Y-m-d') . ' ' . $time->format('H:i:s'));

        return $this;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDate(\DateTime $date): Track
    {
        $newDate = new \DateTime($this->startDateTime->format('Y-m-d') . ' 00:00:00');

        $this->startDateTime = $newDate->add($newDate->diff($date));

        return $this;
    }

    public function setSource(string $source): Track
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setStravaActivityId(int $stravaActivityId): Track
    {
        $this->stravaActitityId = $stravaActivityId;

        return $this;
    }

    public function getStravaActivityId(): ?int
    {
        return (int)$this->stravaActitityId;
    }

    public function setGeoJson(string $geoJson): Track
    {
        $this->geoJson = $geoJson;

        return $this;
    }

    public function getWaypointList(): string
    {
        return $this->geoJson;
    }

    public function isReviewed(): bool
    {
        return $this->reviewed;
    }

    public function setReviewed(bool $reviewed): self
    {
        $this->reviewed = $reviewed;

        return $this;
    }

    public function elasticable(): bool
    {
        return $this->enabled && !$this->deleted && $this->reviewed;
    }
}
