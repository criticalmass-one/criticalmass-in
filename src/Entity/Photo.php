<?php declare(strict_types=1);

namespace App\Entity;

use MalteHuebner\DataQueryBundle\Attribute\EntityAttribute as DataQuery;
use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use MalteHuebner\OrderedEntitiesBundle\Annotation as OE;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;
use App\Criticalmass\Router\Attribute as Routing;
use App\Criticalmass\UploadFaker\FakeUploadable;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\CoordinateInterface;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @OE\OrderedEntity()
 */
#[Vich\Uploadable]
#[Routing\DefaultRoute(name: 'caldera_criticalmass_photo_show_ride')]
#[ORM\Table(name: 'photo')]
#[ORM\Entity(repositoryClass: 'App\Repository\PhotoRepository')]
#[ORM\Index(fields: ['exifCreationDate'], name: 'photo_exif_creation_date_index')]
class Photo implements FakeUploadable, ViewableEntity, ManipulateablePhotoInterface, RouteableInterface, PostableInterface, OrderedEntityInterface, CoordinateInterface
{
    #[Routing\RouteParameter(name: 'id')]
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[DataQuery\Sortable]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'photos')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?User $user = null;

    /**
     * @OE\Identical()
     */
    #[DataQuery\Queryable]
    #[Routing\RouteParameter(name: 'rideIdentifier')]
    #[ORM\ManyToOne(targetEntity: 'Ride', inversedBy: 'photos')]
    #[ORM\JoinColumn(name: 'ride_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?Ride $ride = null;

    #[DataQuery\Queryable]
    #[Routing\RouteParameter(name: 'citySlug')]
    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'photos')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?City $city = null;

    #[DataQuery\Queryable]
    #[DataQuery\Sortable]
    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $latitude = null;

    #[DataQuery\Queryable]
    #[DataQuery\Sortable]
    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $longitude = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'integer')]
    protected int $views = 0;

    /**
     * @OE\Boolean(value=true)
     */
    #[DataQuery\DefaultBooleanValue(alias: 'isEnabled', value: true)]
    #[ORM\Column(type: 'boolean')]
    #[Ignore]
    protected bool $enabled = true;

    /**
     * @OE\Boolean(value=false)
     */
    #[DataQuery\DefaultBooleanValue(alias: 'isDeleted', value: false)]
    #[ORM\Column(type: 'boolean')]
    #[Ignore]
    protected bool $deleted = false;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $creationDateTime = null;

    #[Vich\UploadableField(mapping: 'photo_photo', fileNameProperty: 'imageName', size: 'imageSize', mimeType: 'imageMimeType')]
    protected ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $imageName = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $imageSize = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $imageMimeType = null;

    #[Vich\UploadableField(mapping: 'photo_photo', fileNameProperty: 'backupName')]
    #[JMS\Expose]
    protected ?File $backupFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $backupName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $backupSize = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $backupMimeType = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $updatedAt = null;

    #[ORM\OneToMany(targetEntity: 'Ride', mappedBy: 'featuredPhoto', fetch: 'LAZY')]
    #[Ignore]
    protected Collection $featuredRides;

    #[DataQuery\Queryable]
    #[DataQuery\Sortable]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['ride-list'])]
    protected ?string $location = null;

    #[ORM\OneToMany(targetEntity: 'Post', mappedBy: 'photo')]
    #[Ignore]
    protected Collection $posts;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $exifExposure = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $exifAperture = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'smallint', nullable: true)]
    protected ?int $exifIso = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $exifFocalLength = null;

    #[DataQuery\Sortable]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $exifCamera = null;

    /**
     * @OE\Order(direction="asc")
     */
    #[DataQuery\DateTimeQueryable(format: 'strict_date_hour_minute_second', pattern: 'Y-m-d\TH:i:s')]
    #[DataQuery\Sortable]
    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $exifCreationDate = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->featuredRides = new ArrayCollection();
        $this->exifCreationDate = new \DateTime();
        $this->creationDateTime = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->description = '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city): Photo
    {
        $this->city = $city;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): Photo
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): Photo
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude = null): CoordinateInterface
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude = null): CoordinateInterface
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): Photo
    {
        $this->ride = $ride;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Photo
    {
        $this->user = $user;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): Photo
    {
        $this->description = $description;

        return $this;
    }

    public function hasCoordinates(): bool
    {
        return ($this->latitude && $this->longitude);
    }

    public function setCreationDateTime(\DateTime $creationDateTime): Photo
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    public function getCreationDateTime(): \DateTime
    {
        return $this->creationDateTime;
    }

    public function setImageFile(File $image = null): PhotoInterface
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(string $imageName = null): PhotoInterface
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(int $imageSize = null): PhotoInterface
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageMimeType(): ?string
    {
        return $this->imageMimeType;
    }

    public function setImageMimeType(string $imageMimeType = null): PhotoInterface
    {
        $this->imageMimeType = $imageMimeType;

        return $this;
    }

    public function setBackupFile(File $image = null): Photo
    {
        $this->backupFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getBackupFile(): ?File
    {
        return $this->imageFile;
    }

    public function setBackupName(string $backupName = null): ManipulateablePhotoInterface
    {
        $this->backupName = $backupName;

        return $this;
    }

    public function getBackupName(): ?string
    {
        return $this->backupName;
    }

    public function getBackupSize(): ?int
    {
        return $this->backupSize;
    }

    public function setBackupSize(int $backupSize): Photo
    {
        $this->backupSize = $backupSize;

        return $this;
    }

    public function getBackupMimeType(): ?string
    {
        return $this->backupMimeType;
    }

    public function setBackupMimeType(string $backupMimeType): Photo
    {
        $this->backupMimeType = $backupMimeType;

        return $this;
    }

    public function setViews(int $views): ViewableEntity
    {
        $this->views = $views;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function incViews(): ViewableEntity
    {
        ++$this->views;

        return $this;
    }

    public function setUpdatedAt($updatedAt): Photo
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }


    public function setLocation(string $location = null): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setPosts(Collection $posts): Photo
    {
        $this->posts = $posts;

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getExifExposure(): ?string
    {
        return $this->exifExposure;
    }

    public function setExifExposure(string $exifExposure = null): Photo
    {
        $this->exifExposure = $exifExposure;

        return $this;
    }

    public function getExifAperture(): ?string
    {
        return $this->exifAperture;
    }

    public function setExifAperture(string $exifAperture = null): Photo
    {
        $this->exifAperture = $exifAperture;

        return $this;
    }

    public function getExifIso(): ?int
    {
        return $this->exifIso;
    }

    public function setExifIso(int $exifIso = null): Photo
    {
        $this->exifIso = $exifIso;

        return $this;
    }

    public function getExifFocalLength(): ?float
    {
        return $this->exifFocalLength;
    }

    public function setExifFocalLength(float $exifFocalLength = null): Photo
    {
        $this->exifFocalLength = $exifFocalLength;

        return $this;
    }

    public function getExifCamera(): ?string
    {
        return $this->exifCamera;
    }

    public function setExifCamera(string $exifCamera = null): Photo
    {
        $this->exifCamera = $exifCamera;

        return $this;
    }

    public function getExifCreationDate(): \DateTime
    {
        return $this->exifCreationDate;
    }

    public function setExifCreationDate(\DateTime $exifCreationDate): Photo
    {
        $this->exifCreationDate = $exifCreationDate;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function toCoord(): CoordInterface
    {
        return new Coord($this->latitude, $this->longitude);
    }
}
