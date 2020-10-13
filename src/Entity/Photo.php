<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\DataQuery\Annotation\EntityAnnotation as DataQuery;
use App\Criticalmass\Geocoding\ReverseGeocodeable;
use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use MalteHuebner\OrderedEntitiesBundle\Annotation as OE;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;
use App\Criticalmass\Router\Annotation as Routing;
use App\Criticalmass\Sharing\Annotation as Sharing;
use App\Criticalmass\Sharing\ShareableInterface\Shareable;
use App\Criticalmass\UploadFaker\FakeUploadable;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\CoordinateInterface;
use App\EntityInterface\ElasticSearchPinInterface;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use App\EntityInterface\StaticMapableInterface;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="photo")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 * @JMS\ExclusionPolicy("all")
 * @Routing\DefaultRoute(name="caldera_criticalmass_photo_show_ride")
 * @OE\OrderedEntity()
 */
class Photo implements FakeUploadable, ViewableEntity, ManipulateablePhotoInterface, RouteableInterface, PostableInterface, AutoParamConverterAble, Shareable, StaticMapableInterface, ReverseGeocodeable, OrderedEntityInterface, ElasticSearchPinInterface, CoordinateInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @Routing\RouteParameter(name="photoId")
     * @DataQuery\Sortable
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="photos")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="photos")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     * @Routing\RouteParameter(name="rideIdentifier")
     * @OE\Identical()
     * @DataQuery\Queryable
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="photos")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @Routing\RouteParameter(name="citySlug")
     * @DataQuery\Queryable
     */
    protected $city;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     * @var float $latitude
     */
    protected $latitude = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     * @var float $longitude
     */
    protected $longitude = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @Sharing\Intro()
     * @DataQuery\Sortable
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     * @DataQuery\Sortable
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="boolean")
     * @OE\Boolean(value=true)
     * @DataQuery\DefaultBooleanValue(value=true, alias="isEnabled")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     * @OE\Boolean(value=false)
     * @DataQuery\DefaultBooleanValue(value=false, alias="isDeleted")
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     * @DataQuery\Sortable
     */
    protected $creationDateTime;

    /**
     * @var File $imageFile
     * @Vich\UploadableField(mapping="photo_photo", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType")
     */
    protected $imageFile;

    /**
     * @var string
     * @var string $imageName
     * @ORM\Column(type="string", length=255)
     */
    protected $imageName;

    /**
     * @var int $imageSize
     * @ORM\Column(type="integer", nullable=true)
     * @DataQuery\Sortable
     */
    protected $imageSize;

    /**
     * @var string $imageMimeType
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageMimeType;

    /**
     * @var string $imageGoogleCloudHash
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageGoogleCloudHash;

    /**
     * @var File
     * @Vich\UploadableField(mapping="photo_photo", fileNameProperty="backupName")
     */
    protected $backupFile;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $backupName;

    /**
     * @var int $backupSize
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $backupSize;

    /**
     * @var string $backupMimeType
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $backupMimeType;

    /**
     * @var string $backupGoogleCloudHash
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $backupGoogleCloudHash;

    /**
     * @ORM\Column(type="datetime")
     * @DataQuery\Sortable
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="featuredPhoto", fetch="LAZY")
     */
    protected $featuredRides;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Sharing\Shorturl()
     */
    protected $shorturl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @DataQuery\Sortable
     */
    protected $location;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="photo")
     */
    protected $posts;

    /**
     * @var string $exifExposure
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose
     * @DataQuery\Sortable
     */
    protected $exifExposure;

    /**
     * @var string $exifAperture
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose
     * @DataQuery\Sortable
     */
    protected $exifAperture;

    /**
     * @var int $exifIso
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Expose
     * @DataQuery\Sortable
     */
    protected $exifIso;

    /**
     * @var float $exifFocalLength
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     * @DataQuery\Sortable
     */
    protected $exifFocalLength;

    /**
     * @var string $exifCamera
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     * @DataQuery\Sortable
     */
    protected $exifCamera;

    /**
     * @var \DateTime $exifCreationDate
     * @ORM\Column(type="datetime")
     * @OE\Order(direction="asc")
     * @JMS\Expose
     * @DataQuery\DateTimeQueryable(format="strict_date_hour_minute_second", pattern="Y-m-d\TH:i:s")
     * @DataQuery\Sortable
     */
    protected $exifCreationDate;

    public function __construct()
    {
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


    public function setShorturl(string $shorturl): Photo
    {
        $this->shorturl = $shorturl;

        return $this;
    }

    public function getShorturl(): ?string
    {
        return $this->shorturl;
    }

    public function setLocation(string $location = null): ReverseGeocodeable
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

    public function getImageGoogleCloudHash(): ?string
    {
        return $this->imageGoogleCloudHash;
    }

    public function setImageGoogleCloudHash(string $imageGoogleCloudHash): Photo
    {
        $this->imageGoogleCloudHash = $imageGoogleCloudHash;

        return $this;
    }

    public function getBackupGoogleCloudHash(): ?string
    {
        return $this->backupGoogleCloudHash;
    }

    public function setBackupGoogleCloudHash(string $backupGoogleCloudHash): Photo
    {
        $this->backupGoogleCloudHash = $backupGoogleCloudHash;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    /**
     * @DataQuery\Queryable
     */
    public function getPin(): string
    {
        return sprintf('%f,%f', $this->latitude, $this->longitude);
    }

    public function elasticable(): bool
    {
        return $this->ride && $this->enabled && !$this->deleted;
    }

    public function toCoord(): CoordInterface
    {
        return new Coord($this->latitude, $this->longitude);
    }
}
