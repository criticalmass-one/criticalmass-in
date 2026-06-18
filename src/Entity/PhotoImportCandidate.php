<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A photo uploaded via the unified upload that is staged for review before it
 * becomes a real Photo on a ride. Mirrors TrackImportCandidate for the photo side:
 * the binary lives in the photo-candidate staging filesystem (outside the web root),
 * the entity holds the metadata needed to group photos into galleries (exifCreationDate)
 * and to match a gallery to a ride (date + GPS).
 */
#[ORM\Table(name: 'photo_candidate')]
#[ORM\Entity(repositoryClass: 'App\Repository\PhotoImportCandidateRepository')]
class PhotoImportCandidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected ?User $user = null;

    /** Suggested / assigned ride, null while parked for manual review. */
    #[ORM\ManyToOne(targetEntity: Ride::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Ride $ride = null;

    /** SHA1 of the uploaded file, for duplicate detection. */
    #[ORM\Column(type: 'string', length: 40)]
    protected ?string $fileHash = null;

    /** Path in the photo-candidate staging filesystem (e.g. "<hash>.jpg"). */
    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $stagedFilename = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $originalName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $mimeType = null;

    /** EXIF capture date — the gallery grouping key and the matching date. */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $exifCreationDate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $longitude = null;

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    protected bool $rejected = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
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

    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    public function setFileHash(string $fileHash): self
    {
        $this->fileHash = $fileHash;

        return $this;
    }

    public function getStagedFilename(): ?string
    {
        return $this->stagedFilename;
    }

    public function setStagedFilename(string $stagedFilename): self
    {
        $this->stagedFilename = $stagedFilename;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getExifCreationDate(): ?\DateTime
    {
        return $this->exifCreationDate;
    }

    public function setExifCreationDate(?\DateTime $exifCreationDate): self
    {
        $this->exifCreationDate = $exifCreationDate;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isRejected(): bool
    {
        return $this->rejected;
    }

    public function setRejected(bool $rejected): self
    {
        $this->rejected = $rejected;

        return $this;
    }

    /**
     * Gallery grouping key: the EXIF capture day, or null when undated.
     */
    public function getGalleryKey(): ?string
    {
        return $this->exifCreationDate?->format('Y-m-d');
    }
}
