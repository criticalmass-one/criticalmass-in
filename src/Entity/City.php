<?php declare(strict_types=1);

namespace App\Entity;

use MalteHuebner\DataQueryBundle\Annotation\EntityAnnotation as DataQuery;
use App\Criticalmass\Router\Annotation as Routing;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\AuditableInterface;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\BoardInterface;
use App\EntityInterface\CoordinateInterface;
use App\EntityInterface\ElasticSearchPinInterface;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use App\EntityInterface\StaticMapableInterface;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @Vich\Uploadable
 * @ORM\Table(name="city")
 * @JMS\ExclusionPolicy("all")
 * @Routing\DefaultRoute(name="caldera_criticalmass_city_show")
 */
class City implements BoardInterface, ViewableEntity, ElasticSearchPinInterface, PhotoInterface, RouteableInterface, AuditableInterface, AutoParamConverterAble, SocialNetworkProfileAble, PostableInterface, StaticMapableInterface, CoordinateInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @DataQuery\Sortable
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="cities", cascade={"persist"})
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     * @DataQuery\Queryable
     * @DataQuery\Sortable
     */
    protected ?Region $region = null;

    /**
     * @ORM\ManyToOne(targetEntity="CitySlug", inversedBy="cities")
     * @ORM\JoinColumn(name="main_slug_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @Routing\RouteParameter(name="citySlug")
     */
    protected ?CitySlug $mainSlug = null;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\SerializedName("name")
     * @JMS\Groups({"ride-list"})
     * @DataQuery\Sortable
     */
    protected ?string $city = null;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @DataQuery\Sortable
     */
    protected ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?string $description = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @DataQuery\Queryable
     */
    protected float $latitude = 0.0;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @DataQuery\Queryable
     */
    protected float $longitude = 0.0;

    /**
     * @ORM\Column(type="boolean")
     * @DataQuery\DefaultBooleanValue(value=true)
     */
    protected bool $enabled = true;

    /**
     * @ORM\OneToMany(targetEntity="Ride", mappedBy="city")
     */
    protected Collection $rides;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="city")
     */
    protected Collection $posts;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="city")
     */
    protected Collection $photos;

    /**
     * @ORM\OneToMany(targetEntity="CitySlug", mappedBy="city", cascade={"persist", "remove"})
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected Collection $slugs;

    /**
     * @ORM\OneToMany(targetEntity="CityCycle", mappedBy="city", cascade={"persist", "remove"})
     */
    protected Collection $cycles;

    /**
     * @ORM\OneToMany(targetEntity="SocialNetworkProfile", mappedBy="city", cascade={"persist", "remove"})
     * @JMS\Expose
     */
    protected Collection $socialNetworkProfiles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="int")
     * @JMS\Expose
     * @DataQuery\Queryable
     * @DataQuery\Sortable
     */
    protected ?int $cityPopulation = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose
     */
    protected ?string $punchLine = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected ?string $longDescription = null;

    /**
     * @Vich\UploadableField(mapping="city_photo", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType")
     */
    protected ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $imageName = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $imageSize = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $imageMimeType = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @DataQuery\Sortable
     */
    private ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @DataQuery\Sortable
     */
    protected ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $enableBoard = false;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected string $timezone = 'Europe/Berlin';

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected int $threadNumber = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected int $postNumber = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $colorRed = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $colorGreen = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $colorBlue = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Thread", inversedBy="cities")
     * @ORM\JoinColumn(name="lastthread_id", referencedColumnName="id")
     */
    protected ?Thread $lastThread = null;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $views = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $rideNamer = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $wikidataEntityId = null;

    public function __construct()
    {
        $this->rides = new ArrayCollection();
        $this->slugs = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->cycles = new ArrayCollection();
        $this->socialNetworkProfiles = new ArrayCollection();

        $this->createdAt = new \DateTime();
    }

    public function setRegion(Region $region): City
    {
        $this->region = $region;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): City
    {
        $this->user = $user;

        return $this;
    }

    public function getMainSlug(): CitySlug
    {
        return $this->mainSlug;
    }

    public function setMainSlug(CitySlug $citySlug): City
    {
        $this->mainSlug = $citySlug;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getCity();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("slug")
     * @JMS\Type("string")
     * @deprecated
     */
    public function getMainSlugString(): string
    {
        return $this->getMainSlug()->getSlug();
    }

    /**
     * @deprecated
     */
    public function getSlug(): string
    {
        return $this->getMainSlugString();
    }

    public function setId(int $id): City
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCity(string $city): City
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setTitle(string $title): BoardInterface
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setLatitude(float $latitude = null): CoordinateInterface
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude = null): CoordinateInterface
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function addRide(Ride $rides): City
    {
        $this->rides[] = $rides;

        return $this;
    }

    public function removeRide(Ride $ride): City
    {
        $this->rides->removeElement($ride);

        return $this;
    }

    public function getRides(): Collection
    {
        return $this->rides;
    }

    public function addSlug(CitySlug $slug): City
    {
        if (!$this->mainSlug) {
            $this->mainSlug = $slug;
        }

        $this->slugs[] = $slug;

        return $this;
    }

    public function removeSlug(CitySlug $slugs): City
    {
        $this->slugs->removeElement($slugs);

        return $this;
    }

    public function getSlugs(): Collection
    {
        return $this->slugs;
    }

    public function setDescription(string $description): City
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /** @deprecated */
    public function isEqual(City $city): bool
    {
        return $city->getId() === $this->getId();
    }

    public function setEnabled(bool $enabled): City
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setCityPopulation(int $cityPopulation): City
    {
        $this->cityPopulation = $cityPopulation;

        return $this;
    }

    public function getCityPopulation(): ?int
    {
        return $this->cityPopulation;
    }

    public function setPunchLine(string $punchLine): City
    {
        $this->punchLine = $punchLine;

        return $this;
    }

    public function getPunchLine(): ?string
    {
        return $this->punchLine;
    }

    public function setLongDescription(string $longDescription = null): City
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    /**
     * @deprecated
     */
    public function countRides(): int
    {
        return count($this->rides);
    }

    /**
     * @deprecated
     */
    public function getCurrentRide(): ?Ride
    {
        $currentRide = null;
        $dateTime = new \DateTime();

        foreach ($this->getRides() as $ride) {
            if ($ride && !$currentRide && $ride->getDateTime() > $dateTime) {
                $currentRide = $ride;
            } elseif ($ride && $currentRide && $ride->getDateTime() < $currentRide->getDateTime() && $ride->getDateTime() > $dateTime) {
                $currentRide = $ride;
            }
        }

        return $currentRide;
    }

    public function addPost(Post $post): City
    {
        $this->posts->add($post);

        return $this;
    }

    public function removePost(Post $posts): City
    {
        $this->posts->removeElement($posts);

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
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

    /**
     * @DataQuery\Queryable
     */
    public function getPin(): string
    {
        return sprintf('%f,%f', $this->latitude, $this->longitude);
    }

    public function getCoord(): Coord
    {
        return new Coord($this->latitude, $this->longitude);
    }

    public function setEnableBoard(bool $enableBoard): City
    {
        $this->enableBoard = $enableBoard;

        return $this;
    }

    public function getEnableBoard(): bool
    {
        return $this->enableBoard;
    }

    public function setTimezone(string $timezone): City
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): City
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setLastThread(Thread $lastThread = null): BoardInterface
    {
        $this->lastThread = $lastThread;

        return $this;
    }

    public function getLastThread(): ?Thread
    {
        return $this->lastThread;
    }

    public function setPostNumber(int $postNumber): BoardInterface
    {
        $this->postNumber = $postNumber;

        return $this;
    }

    public function getPostNumber(): int
    {
        return $this->postNumber;
    }

    public function incPostNumber(): BoardInterface
    {
        ++$this->postNumber;

        return $this;
    }

    public function setThreadNumber(int $threadNumber): BoardInterface
    {
        $this->threadNumber = $threadNumber;

        return $this;
    }

    public function getThreadNumber(): int
    {
        return $this->threadNumber;
    }

    public function incThreadNumber(): BoardInterface
    {
        ++$this->threadNumber;

        return $this;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): City
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setColorRed(int $colorRed): City
    {
        $this->colorRed = $colorRed;

        return $this;
    }

    public function getColorRed(): int
    {
        return $this->colorRed;
    }

    public function setColorGreen(int $colorGreen): City
    {
        $this->colorGreen = $colorGreen;

        return $this;
    }

    public function getColorGreen(): int
    {
        return $this->colorGreen;
    }

    public function setColorBlue(int $colorBlue): City
    {
        $this->colorBlue = $colorBlue;

        return $this;
    }

    public function getColorBlue(): int
    {
        return $this->colorBlue;
    }

    public function addPhoto(Photo $photo): City
    {
        $this->photos->add($photo);

        return $this;
    }

    public function removePhoto(Photo $photo): City
    {
        $this->photos->removeElement($photo);

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

    public function getCountry(): ?Region
    {
        if ($this->region) {
            return $this->region->getParent();
        }

        return null;
    }

    public function getContinent(): ?Region
    {
        if ($this->getCountry()) {
            return $this->getCountry()->getParent();
        }

        return null;
    }

    public function getDateTime(): ?\DateTime
    {
        return null;
    }

    public function addCycle(CityCycle $cityCycle): City
    {
        $this->cycles->add($cityCycle);

        return $this;
    }

    public function setCycles(Collection $cityCycles): City
    {
        $this->cycles = $cityCycles;

        return $this;
    }

    public function getCycles(): Collection
    {
        return $this->cycles;
    }

    public function removeCycle(CityCycle $cityCycle): City
    {
        $this->cycles->removeElement($cityCycle);

        return $this;
    }

    public function addSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): City
    {
        $this->socialNetworkProfiles->add($socialNetworkProfile);

        return $this;
    }

    public function setSocialNetworkProfiles(Collection $socialNetworkProfiles): City
    {
        $this->socialNetworkProfiles = $socialNetworkProfiles;

        return $this;
    }

    public function getSocialNetworkProfiles(): Collection
    {
        return $this->socialNetworkProfiles;
    }

    public function removeSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): City
    {
        $this->socialNetworkProfiles->removeElement($socialNetworkProfile);

        return $this;
    }

    public function setRideNamer(string $rideNamer): City
    {
        $this->rideNamer = $rideNamer;

        return $this;
    }

    public function getRideNamer(): ?string
    {
        return $this->rideNamer;
    }

    public function setWikidataEntityId(string $wikidataEntityId): City
    {
        $this->wikidataEntityId = $wikidataEntityId;

        return $this;
    }

    public function getWikidataEntityId(): ?string
    {
        return $this->wikidataEntityId;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("color")
     * @JMS\Type("array")
     */
    public function getColor(): array
    {
        return [
            'red' => $this->colorRed,
            'green' => $this->colorGreen,
            'blue' => $this->colorBlue,
        ];
    }

    public function toCoord(): CoordInterface
    {
        return new Coord($this->latitude, $this->longitude);
    }
}
