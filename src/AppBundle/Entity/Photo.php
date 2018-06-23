<?php

namespace AppBundle\Entity;

use AppBundle\EntityInterface\AutoParamConverterAble;
use AppBundle\EntityInterface\PhotoInterface;
use AppBundle\EntityInterface\PostableInterface;
use AppBundle\EntityInterface\RouteableInterface;
use AppBundle\EntityInterface\ViewableInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="photo")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhotoRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Photo implements ViewableInterface, PhotoInterface, RouteableInterface, PostableInterface, AutoParamConverterAble
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
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
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="photos")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $longitude;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $dateTime;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $creationDateTime;

    /**
     * @Vich\UploadableField(mapping="photo_photo", fileNameProperty="imageName")
     *
     * @var File
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $imageName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $backupName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="featuredPhoto", fetch="LAZY")
     */
    protected $featuredRides;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
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

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): Photo
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getDeleted(): bool
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

    public function setLatitude(float $latitude = null): Photo
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude = null): Photo
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getRide(): Ride
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

    public function setDescription(string $description): Photo
    {
        $this->description = $description;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): Photo
    {
        $this->dateTime = $dateTime;

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

    public function setImageFile(File $image = null): Photo
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

    public function setViews(int $views): ViewableInterface
    {
        $this->views = $views;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function incViews(): ViewableInterface
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

    public function setBackupName(string $backupName): Photo
    {
        $this->backupName = $backupName;

        return $this;
    }

    public function getBackupName(): string
    {
        return $this->backupName;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
