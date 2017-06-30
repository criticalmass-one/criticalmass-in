<?php

namespace AppBundle\Entity;

use Caldera\GeoBundle\Entity\Track as BaseTrack;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="track")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrackRepository")
 * @Vich\Uploadable
 * @JMS\ExclusionPolicy("all")
 */
class Track extends BaseTrack
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
     */
    protected $id;

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
     */
    protected $rideEstimate;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $creationDateTime;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $startDateTime;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $endDateTime;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $distance;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $points;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $startPoint;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $endPoint;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

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
        parent::__construct();
    }

    public function getId(): ?int
    {
        return $this->id;
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


    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("colorRed")
     */
    public function getColorRed(): ?int
    {
        if ($this->getUser()) {
            return $this->getUser()->getColorRed();
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
        }

        return null;
    }

    public function __toString(): string
    {
        $result = $this->getUser()->getUsername() . '(';

        if ($this->getCreationDateTime()) {
            $result .= $this->getCreationDateTime()->format('Y-m-d');
        }

        if ($this->getRide()) {
            $result .= ', ' . $this->getRide()->getCity()->getCity();
        }

        $result .= ')';

        return $result;
    }

    /** @deprecated  */
    public function getDurationInterval(): ?\DateInterval
    {
        if ($this->startDateTime && $this->endDateTime) {
            return $this->getEndDateTime()->diff($this->getStartDateTime());
        }

        return null;
    }

    /** @deprecated */
    public function getDurationInSeconds(): int
    {
        if ($this->startDateTime && $this->endDateTime) {
            return $this->getEndDateTime()->getTimestamp() - $this->getStartDateTime()->getTimestamp();
        }

        return 0;
    }

    /** @deprecated  */
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

    /** @deprecated  */
    public function getStartTime(): \DateTime
    {
        return $this->startDateTime;
    }

    /** @deprecated  */
    public function setStartTime(\DateTime $time): Track
    {
        $this->startDateTime = new \DateTime($this->startDateTime->format('Y-m-d') . ' ' . $time->format('H:i:s'));

        return $this;
    }

    /** @deprecated  */
    public function getStartDate(): \DateTime
    {
        return $this->startDateTime;
    }

    /** @deprecated  */
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
}
