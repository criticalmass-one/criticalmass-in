<?php declare(strict_types=1);

namespace App\Entity;

use MalteHuebner\DataQueryBundle\Annotation\EntityAnnotation as DataQuery;
use MalteHuebner\OrderedEntitiesBundle\Annotation as OE;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;
use App\Criticalmass\Router\Annotation as Routing;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\AuditableInterface;
use App\EntityInterface\CoordinateInterface;
use App\EntityInterface\ElasticSearchPinInterface;
use App\EntityInterface\ParticipateableInterface;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use App\EntityInterface\StaticMapableInterface;
use App\Validator\Constraint as CriticalAssert;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="ride")
 * @ORM\Entity(repositoryClass="App\Repository\RideRepository")
 * @JMS\ExclusionPolicy("all")
 * @CriticalAssert\SingleRideForDay
 * @Vich\Uploadable
 * @Routing\DefaultRoute(name="caldera_criticalmass_ride_show")
 */
class Ride implements ParticipateableInterface, ViewableEntity, ElasticSearchPinInterface, PhotoInterface, RouteableInterface, AuditableInterface, PostableInterface, SocialNetworkProfileAble, StaticMapableInterface, OrderedEntityInterface, CoordinateInterface
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
     * @ORM\ManyToOne(targetEntity="User", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="CityCycle", inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="cycle_id", referencedColumnName="id")
     * @JMS\Groups({"extended-ride-list"})
     * @JMS\Expose
     */
    protected ?CityCycle $cycle = null;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @JMS\Groups({"extended-ride-list"})
     * @JMS\Expose
     * @Routing\RouteParameter(name="citySlug")
     * @OE\Identical()
     * @DataQuery\Queryable
     */
    protected ?City $city = null;

    /**
     * @ORM\OneToMany(targetEntity="Track", mappedBy="ride", fetch="LAZY")
     * @JMS\Groups({"extended-ride-list"})
     */
    protected Collection $tracks;

    /**
     * @ORM\OneToMany(targetEntity="Subride", mappedBy="ride", fetch="LAZY")
     * @JMS\Groups({"extended-ride-list"})
     */
    protected Collection $subrides;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?string $slug = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    #[Assert\NotBlank]
    protected ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?string $description = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?string $socialDescription = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @JMS\Type("DateTime<'U'>")
     * @OE\Order(direction="asc")
     * @DataQuery\Sortable
     * @DataQuery\DateTimeQueryable(format="strict_date", pattern="Y-m-d")
     */
    protected \DateTime $dateTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?string $location = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?float $latitude = 0.0;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?float $longitude = 0.0;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?int $estimatedParticipants = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?float $estimatedDistance = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected ?float $estimatedDuration = null;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="ride", fetch="LAZY")
     * @JMS\Groups({"extended-ride-list"})
     */
    protected Collection $posts;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="ride", fetch="LAZY")
     * @JMS\Groups({"extended-ride-list"})
     */
    protected Collection $photos;

    /**
     * @ORM\OneToMany(targetEntity="SocialNetworkProfile", mappedBy="ride", cascade={"persist", "remove"})
     * @JMS\Groups({"extended-ride-list"})
     * @JMS\Expose
     */
    protected ?Collection $socialNetworkProfiles = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected \DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     */
    protected int $participationsNumberYes = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     */
    protected int $participationsNumberMaybe = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose
     */
    protected int $participationsNumberNo = 0;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="ride", fetch="LAZY")
     */
    protected Collection $participations;

    /**
     * @ORM\OneToMany(targetEntity="RideEstimate", mappedBy="ride", fetch="LAZY")
     */
    protected Collection $estimates;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected int $views = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", fetch="LAZY")
     * @ORM\JoinColumn(name="featured_photo", referencedColumnName="id")
     */
    protected ?Photo $featuredPhoto = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected bool $restrictedPhotoAccess = false;

    /**
     * @ORM\OneToMany(targetEntity="Weather", mappedBy="ride", fetch="LAZY")
     * @ORM\OrderBy({"creationDateTime":"DESC"})
     */
    protected Collection $weathers;

    /**
     * @Vich\UploadableField(mapping="ride_photo", fileNameProperty="imageName",  size="imageSize", mimeType="imageMimeType")
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
     * @ORM\Column(type="boolean", options={"default"=true})
     * @OE\Boolean(true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected bool $enabled = true;

    /**
     * @ORM\Column(type="RideDisabledReasonType", nullable=true)
     * @DoctrineAssert\Enum(entity="App\DBAL\Type\RideDisabledReasonType")
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected ?string $disabledReason = null;

    /**
     * @ORM\Column(type="RideType", nullable=true)
     * @DoctrineAssert\Enum(entity="App\DBAL\Type\RideType")
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected ?string $rideType = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrackImportCandidate", mappedBy="ride")
     */
    private Collection $trackImportCandidates;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    private ?string $disabledReasonMessage = null;

    /**
     * @ORM\OneToMany(targetEntity=RideView::class, mappedBy="ride", fetch="LAZY")
     */
    protected Collection $viewRelation;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
        $this->createdAt = new \DateTime();

        $this->weathers = new ArrayCollection();
        $this->estimates = new ArrayCollection();
        $this->tracks = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->subrides = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->socialNetworkProfiles = new ArrayCollection();
        $this->trackImportCandidates = new ArrayCollection();
        $this->viewRelation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): Ride
    {
        $this->user = $user;

        return $this;
    }

    public function getCycle(): ?CityCycle
    {
        return $this->cycle;
    }

    public function setCycle(CityCycle $cityCycle = null): Ride
    {
        $this->cycle = $cityCycle;

        return $this;
    }

    public function setDateTime(\DateTime $dateTime = null): Ride
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
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

    public function setCity(City $city = null): Ride
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
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

    public function setCoord(Coord $coord): Ride
    {
        $this->latitude = $coord->getLatitude();
        $this->longitude = $coord->getLongitude();

        return $this;
    }

    public function getCoord(): Coord
    {
        return new Coord($this->latitude, $this->longitude);
    }

    public function setSlug(string $slug = null): Ride
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function hasSlug(): bool
    {
        return $this->slug !== null;
    }

    public function setTitle(string $title = null): Ride
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): Ride
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setSocialDescription(string $socialDescription): Ride
    {
        $this->socialDescription = $socialDescription;

        return $this;
    }

    public function getSocialDescription(): ?string
    {
        return $this->socialDescription;
    }

    /** @deprecated */
    public function isEqual(Ride $ride): bool
    {
        return $ride->getId() == $this->getId();
    }

    /** @deprecated */
    public function equals(Ride $ride): bool
    {
        return $this->isEqual($ride);
    }

    /** @deprecated */
    public function isSameRide(Ride $ride): bool
    {
        return $ride->getCity()->getId() == $this->getCity()->getId() && $ride->getDateTime()->format('Y-m-d') == $this->getDateTime()->format('Y-m-d');
    }

    public function __toString(): string
    {
        if ($this->city) {
            return $this->city->getTitle() . " - " . $this->getDateTime()->format('Y-m-d');
        } else {
            return $this->getDateTime()->format('Y-m-d');
        }
    }

    public function setEstimatedParticipants(int $estimatedParticipants): Ride
    {
        $this->estimatedParticipants = $estimatedParticipants;

        return $this;
    }

    public function getEstimatedParticipants(): ?int
    {
        return $this->estimatedParticipants;
    }

    /** @deprecated */
    public function getDate(): \DateTime
    {
        return $this->dateTime;
    }

    /** @deprecated */
    public function getTime(): \DateTime
    {
        return $this->dateTime;
    }

    /** @deprecated */
    public function setDate(\DateTime $date): Ride
    {
        $this->dateTime = new \DateTime($date->format('Y-m-d') . ' ' . $this->dateTime->format('H:i:s'),
            $date->getTimezone());

        return $this;
    }

    /** @deprecated */
    public function setTime(\DateTime $time): Ride
    {
        $this->dateTime = new \DateTime($this->dateTime->format('Y-m-d') . ' ' . $time->format('H:i:s'),
            $time->getTimezone());

        return $this;
    }

    public function setEstimatedDistance(float $estimatedDistance): Ride
    {
        $this->estimatedDistance = $estimatedDistance;

        return $this;
    }

    public function getEstimatedDistance(): ?float
    {
        return $this->estimatedDistance;
    }

    public function setEstimatedDuration(float $estimatedDuration): Ride
    {
        $this->estimatedDuration = $estimatedDuration;

        return $this;
    }

    public function getEstimatedDuration(): ?float
    {
        return $this->estimatedDuration;
    }

    public function getEstimatedDurationInSeconds(): ?int
    {
        if ($this->estimatedDuration) {
            return intval(ceil($this->estimatedDuration * 60 * 60));
        }

        return null;
    }

    public function addTrack(Track $track): Ride
    {
        $this->tracks->add($track);

        return $this;
    }

    public function removeTrack(Track $track): Ride
    {
        $this->tracks->removeElement($track);

        return $this;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addPost(Post $post): Ride
    {
        $this->posts->add($post);

        return $this;
    }

    public function removePost(Post $posts): Ride
    {
        $this->posts->removeElement($posts);

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPhoto(Photo $photo): Ride
    {
        $this->photos->add($photo);

        return $this;
    }

    public function removePhoto(Photo $photos): Ride
    {
        $this->photos->removeElement($photos);

        return $this;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addSubride(Subride $subride): Ride
    {
        $this->subrides->add($subride);

        return $this;
    }

    public function removeSubride(Subride $subrides): Ride
    {
        $this->subrides->removeElement($subrides);

        return $this;
    }

    public function getSubrides(): Collection
    {
        return $this->subrides;
    }

    public function getDurationInterval(): \DateInterval
    {
        $totalMinutes = $this->estimatedDuration * 60.0;

        $hours = floor($totalMinutes / 60.0);
        $minutes = $totalMinutes % 60.0;

        return new \DateInterval('PT' . $hours . 'H' . $minutes . 'M');
    }

    public function getAverageVelocity(): float
    {
        if (!$this->getEstimatedDuration() || !$this->getEstimatedDistance()) {
            return 0;
        }

        return $this->getEstimatedDistance() / $this->getEstimatedDuration();
    }

    /**
     * @DataQuery\Queryable
     */
    public function getPin(): string
    {
        if (!$this->latitude || !$this->longitude) {
            return '0,0';
        }

        return sprintf('%f,%f', $this->latitude, $this->longitude);
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Ride
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): Ride
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setParticipationsNumberYes(int $participationsNumberYes): ParticipateableInterface
    {
        $this->participationsNumberYes = $participationsNumberYes;

        return $this;
    }

    public function getParticipationsNumberYes(): int
    {
        return $this->participationsNumberYes;
    }

    public function setParticipationsNumberMaybe(int $participationsNumberMaybe): ParticipateableInterface
    {
        $this->participationsNumberMaybe = $participationsNumberMaybe;

        return $this;
    }

    public function getParticipationsNumberMaybe(): int
    {
        return $this->participationsNumberMaybe;
    }

    public function setParticipationsNumberNo(int $participationsNumberNo): ParticipateableInterface
    {
        $this->participationsNumberNo = $participationsNumberNo;

        return $this;
    }

    public function getParticipationsNumberNo(): int
    {
        return $this->participationsNumberNo;
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

    /**
     * @DataQuery\Queryable
     */
    public function getRegion(): ?Region
    {
        if ($this->city) {
            return $this->city->getRegion();
        }

        return null;
    }

    public function getCountry(): ?Region
    {
        if ($this->city) {
            return $this->city->getCountry();
        }

        return null;
    }

    public function getIsEnabled(): ?bool
    {
        if ($this->city) {
            return $this->city->isEnabled();
        }

        return null;
    }

    public function setFeaturedPhoto(Photo $featuredPhoto = null): Ride
    {
        $this->featuredPhoto = $featuredPhoto;

        return $this;
    }

    public function getFeaturedPhoto(): ?Photo
    {
        return $this->featuredPhoto;
    }

    /** @deprecated */
    public function getRestrictedPhotoAccess(): bool
    {
        return $this->restrictedPhotoAccess;
    }

    /** @deprecated */
    public function setRestrictedPhotoAccess(bool $restrictedPhotoAccess): Ride
    {
        $this->restrictedPhotoAccess = $restrictedPhotoAccess;

        return $this;
    }

    public function addParticipation(Participation $participation): Ride
    {
        $this->participations->add($participation);

        return $this;
    }

    public function removeParticipation(Participation $participation): Ride
    {
        $this->participations->removeElement($participation);

        return $this;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addEstimate(RideEstimate $estimate): Ride
    {
        $this->estimates->add($estimate);

        return $this;
    }

    public function removeEstimate(RideEstimate $estimate): Ride
    {
        $this->estimates->removeElement($estimate);

        return $this;
    }

    public function getEstimates(): Collection
    {
        return $this->estimates;
    }

    public function setEstimates(Collection $estimates): Ride
    {
        $this->estimates = $estimates;

        return $this;
    }

    public function addWeather(Weather $weather): self
    {
        $this->weathers->add($weather);

        return $this;
    }

    public function removeWeather(Weather $weather): self
    {
        $this->weathers->removeElement($weather);

        return $this;
    }

    public function getWeathers(): Collection
    {
        return $this->weathers;
    }

    public function setWeathers(Collection $weathers): self
    {
        $this->weathers = $weathers;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
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

    public function addSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): Ride
    {
        $this->socialNetworkProfiles->add($socialNetworkProfile);

        return $this;
    }

    public function setSocialNetworkProfiles(Collection $socialNetworkProfiles): Ride
    {
        $this->socialNetworkProfiles = $socialNetworkProfiles;

        return $this;
    }

    public function getSocialNetworkProfiles(): Collection
    {
        return $this->socialNetworkProfiles;
    }

    public function removeSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): Ride
    {
        $this->socialNetworkProfiles->removeElement($socialNetworkProfile);

        return $this;
    }

    public function setEnabled(bool $enabled): Ride
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getDisabledReason(): ?string
    {
        return $this->disabledReason;
    }

    public function setDisabledReason(string $disabledReason = null): Ride
    {
        $this->disabledReason = $disabledReason;

        return $this;
    }

    public function getRideType(): ?string
    {
        return $this->rideType;
    }

    public function setRideType(string $rideType = null): Ride
    {
        $this->rideType = $rideType;

        return $this;
    }

    /**
     * @return Collection|TrackImportCandidate[]
     */
    public function getTrackImportCandidates(): Collection
    {
        return $this->trackImportCandidates;
    }

    public function addTrackImportCandidate(TrackImportCandidate $trackImportCandidate): self
    {
        if (!$this->trackImportCandidates->contains($trackImportCandidate)) {
            $this->trackImportCandidates[] = $trackImportCandidate;
            $trackImportCandidate->setRide($this);
        }

        return $this;
    }

    public function removeTrackImportCandidate(TrackImportCandidate $trackImportCandidate): self
    {
        if ($this->trackImportCandidates->contains($trackImportCandidate)) {
            $this->trackImportCandidates->removeElement($trackImportCandidate);
            // set the owning side to null (unless already changed)
            if ($trackImportCandidate->getRide() === $this) {
                $trackImportCandidate->setRide(null);
            }
        }

        return $this;
    }

    public function toCoord(): CoordInterface
    {
        return new Coord($this->latitude, $this->longitude);
    }

    public function getDisabledReasonMessage(): ?string
    {
        return $this->disabledReasonMessage;
    }

    public function setDisabledReasonMessage(?string $disabledReasonMessage): self
    {
        $this->disabledReasonMessage = $disabledReasonMessage;

        return $this;
    }

    /**
     * @return Collection|RideView[]
     */
    public function getViewRelations(): Collection
    {
        return $this->viewRelation;
    }

    public function addViewRelation(RideView $viewRelation): self
    {
        if (!$this->viewRelation->contains($viewRelation)) {
            $this->viewRelation[] = $viewRelation;
            $viewRelation->setRide($this);
        }

        return $this;
    }

    public function removeViewRelation(RideView $viewRelation): self
    {
        if ($this->viewRelation->contains($viewRelation)) {
            $this->viewRelation->removeElement($viewRelation);
            // set the owning side to null (unless already changed)
            if ($viewRelation->getRide() === $this) {
                $viewRelation->setRide(null);
            }
        }

        return $this;
    }
}
