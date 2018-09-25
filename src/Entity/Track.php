<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Geo\EntityInterface\TrackInterface;
use App\EntityInterface\RouteableInterface;
use App\EntityInterface\StaticMapableInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Criticalmass\Router\Annotation as Routing;
use Caldera\GeoBasic\Track\TrackInterface as BaseTrackInterface;

/**
 * @ORM\Table(name="track")
 * @ORM\Entity(repositoryClass="App\Repository\TrackRepository")
 * @Vich\Uploadable
 * @JMS\ExclusionPolicy("all")
 * @Routing\DefaultRoute(name="caldera_criticalmass_track_view")
 */
class Track implements RouteableInterface, StaticMapableInterface, TrackInterface
{
    const TRACK_SOURCE_GPX = 'TRACK_SOURCE_GPX';
    const TRACK_SOURCE_STRAVA = 'TRACK_SOURCE_STRAVA';
    const TRACK_SOURCE_RUNKEEPER = 'TRACK_SOURCE_RUNKEEPER';
    const TRACK_SOURCE_RUNTASTIC = 'TRACK_SOURCE_RUNTASTIC';
    const TRACK_SOURCE_DRAW = 'TRACK_SOURCE_DRAW';
    const TRACK_SOURCE_GLYMPSE = 'TRACK_SOURCE_GLYMPSE';
    const TRACK_SOURCE_CRITICALMAPS = 'TRACK_SOURCE_CRITICALMAPS';
    const TRACK_SOURCE_UNKNOWN = 'TRACK_SOURCE_UNKNOWN';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @Routing\RouteParameter(name="trackId")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $username;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="tracks")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tracks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="RideEstimate", mappedBy="track", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="estimate_id", referencedColumnName="id")
     */
    protected $rideEstimate;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $endDateTime;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $distance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $points;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $startPoint;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $endPoint;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $md5Hash;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @deprecated
     */
    protected $latLngList;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $geoJson;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @JMS\SerializedName("polylineString")
     */
    protected $polyline;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @JMS\SerializedName("reducedPolylineString")
     */
    protected $reducedPolyline;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="track_file", fileNameProperty="trackFilename")
     * @var File
     */
    protected $trackFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $trackFilename;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('TRACK_SOURCE_GPX', 'TRACK_SOURCE_STRAVA', 'TRACK_SOURCE_RUNKEEPER', 'TRACK_SOURCE_RUNTASTIC', 'TRACK_SOURCE_DRAW', 'TRACK_SOURCE_GLYMPSE', 'TRACK_SOURCE_CRITICALMAPS', 'TRACK_SOURCE_UNKNOWN')")
     */
    protected $source = self::TRACK_SOURCE_UNKNOWN;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    protected $stravaActitityId;

    public function __construct()
    {
        $this->setCreationDateTime(new \DateTime());
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

    public function setCreationDateTime(\DateTime $creationDateTime): Track
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    public function getCreationDateTime(): \DateTime
    {
        return $this->creationDateTime;
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

    public function setStartDateTime(\DateTime $startDateTime): Track
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getStartDateTime(): ?\DateTime
    {
        if ($this->startDateTime) {
            return $this->startDateTime->setTimezone(new \DateTimeZone('UTC'));
        }

        return null;
    }

    public function setEndDateTime(\DateTime $endDateTime): Track
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function getEndDateTime(): ?\DateTime
    {
        if ($this->endDateTime) {
            return $this->endDateTime->setTimezone(new \DateTimeZone('UTC'));
        }

        return null;
    }

    public function setDistance(float $distance): Track
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setPoints(int $points): Track
    {
        $this->points = $points;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
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

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("colorRed")
     */
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

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("colorGreen")
     */
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

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("colorBlue")
     */
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

    public function setTrackFile(File $track = null): Track
    {
        $this->trackFile = $track;

        if ($track) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getTrackFile(): ?File
    {
        return $this->trackFile;
    }

    public function setTrackFilename(string $trackFilename = null): Track
    {
        $this->trackFilename = $trackFilename;

        return $this;
    }

    public function getTrackFilename(): ?string
    {
        return $this->trackFilename;
    }

    public function setStartPoint(int $startPoint): Track
    {
        if ($startPoint >= 1) {
            $this->startPoint = $startPoint;
        } else {
            $this->startPoint = 1;
        }

        return $this;
    }

    public function getStartPoint(): int
    {
        return $this->startPoint;
    }

    public function setEndPoint(int $endPoint): Track
    {
        if ($endPoint <= $this->points) {
            $this->endPoint = $endPoint;
        } else {
            $this->endPoint = $this->points - 1;
        }

        return $this;
    }

    public function getEndPoint(): int
    {
        return $this->endPoint;
    }

    public function setUpdatedAt(\DateTime $updatedAt): Track
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
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
        return $this->stravaActitityId;
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
}
