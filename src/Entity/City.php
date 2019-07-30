<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\StaticMapableInterface;
use App\Criticalmass\Sharing\ShareableInterface\Shareable;
use Caldera\GeoBasic\Coord\Coord;
use App\EntityInterface\AuditableInterface;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\BoardInterface;
use App\EntityInterface\ElasticSearchPinInterface;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Criticalmass\Router\Annotation as Routing;
use App\Criticalmass\Sharing\Annotation as Sharing;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @Vich\Uploadable
 * @ORM\Table(name="city")
 * @JMS\ExclusionPolicy("all")
 * @Routing\DefaultRoute(name="caldera_criticalmass_city_show")
 */
class City implements BoardInterface, ViewableEntity, ElasticSearchPinInterface, PhotoInterface, RouteableInterface, AuditableInterface, AutoParamConverterAble, SocialNetworkProfileAble, PostableInterface, Shareable, StaticMapableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="cities", cascade={"persist"})
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="CitySlug", inversedBy="cities")
     * @ORM\JoinColumn(name="main_slug_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @Routing\RouteParameter(name="citySlug")
     */
    protected $mainSlug;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\SerializedName("name")
     * @JMS\Groups({"ride-list"})
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @Sharing\Title()
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $twitter;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $latitude = 0;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $longitude = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\OneToMany(targetEntity="Ride", mappedBy="city")
     */
    protected $rides;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="city")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="city")
     */
    protected $photos;

    /**
     * @ORM\OneToMany(targetEntity="CitySlug", mappedBy="city", cascade={"persist", "remove"})
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $slugs;

    /**
     * @ORM\OneToMany(targetEntity="CityCycle", mappedBy="city", cascade={"persist", "remove"})
     */
    protected $cycles;

    /**
     * @ORM\OneToMany(targetEntity="SocialNetworkProfile", mappedBy="city", cascade={"persist", "remove"})
     */
    protected $socialNetworkProfiles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="int")
     * @JMS\Expose
     */
    protected $cityPopulation = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose
     * @Sharing\Intro()
     */
    protected $punchLine;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $longDescription;

    /**
     * @var File $imageFile
     * @Vich\UploadableField(mapping="city_photo", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType")
     */
    protected $imageFile;

    /**
     * @var string $imageName
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageName;

    /**
     * @var int $imageSize
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $imageSize;

    /**
     * @var string $imageMimeType
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageMimeType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enableBoard = false;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $timezone = 'Europe/Berlin';

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $threadNumber = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $postNumber = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $colorRed = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $colorGreen = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $colorBlue = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Thread", inversedBy="cities")
     * @ORM\JoinColumn(name="lastthread_id", referencedColumnName="id")
     */
    protected $lastThread;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Sharing\Shorturl()
     */
    protected $shorturl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $rideNamer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $wikidataEntityId;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Heatmap", mappedBy="city", cascade={"persist", "remove"})
     */
    private $heatmap;

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

    /**
     * @deprecated
     */
    public function setUrl(string $url): City
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @deprecated
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @deprecated
     */
    public function setFacebook(string $facebook): City
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * @deprecated
     */
    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    /**
     * @deprecated
     */
    public function setTwitter(string $twitter): City
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * @deprecated
     */
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setLatitude(float $latitude): City
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): City
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

    public function getCreatedAt(): \DateTime
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

    public function setShorturl(string $shorturl): City
    {
        $this->shorturl = $shorturl;

        return $this;
    }

    public function getShorturl(): ?string
    {
        return $this->shorturl;
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

    public function getHeatmap(): ?Heatmap
    {
        return $this->heatmap;
    }

    public function setHeatmap(?Heatmap $heatmap): self
    {
        $this->heatmap = $heatmap;

        // set (or unset) the owning side of the relation if necessary
        $newCity = $heatmap === null ? null : $this;
        if ($newCity !== $heatmap->getCity()) {
            $heatmap->setCity($newCity);
        }

        return $this;
    }
}
