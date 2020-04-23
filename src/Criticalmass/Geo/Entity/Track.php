<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Entity;

use Caldera\GeoBasic\Track\Track as BaseTrack;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\MappedSuperclass
 * @Vich\Uploadable
 */
class Track extends BaseTrack
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endDateTime;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $distance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $points;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $startPoint;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $endPoint;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $polyline;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $previewPolyline;

    /**
     * @Vich\UploadableField(mapping="track_file", fileNameProperty="trackFilename", size="trackSize", mimeType="trackMimeType")
     */
    protected $trackFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $trackFilename;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $trackSize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $trackMimeType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->setCreationDateTime(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
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
        $this->startPoint = $startPoint;

        return $this;
    }

    public function getStartPoint(): int
    {
        return $this->startPoint;
    }

    public function setEndPoint(int $endPoint): Track
    {
        $this->endPoint = $endPoint;

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

    public function setPreviewPolyline(string $previewPolyline = null): Track
    {
        $this->previewPolyline = $previewPolyline;

        return $this;
    }

    public function getPreviewPolyline(): ?string
    {
        return $this->previewPolyline;
    }
}
