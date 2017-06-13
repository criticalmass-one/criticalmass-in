<?php

namespace AppBundle\Entity;

use AppBundle\EntityInterface\ArchiveableInterface;
use AppBundle\EntityInterface\BoardInterface;
use AppBundle\EntityInterface\ElasticSearchPinInterface;
use AppBundle\EntityInterface\ViewableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Diese Entitaet repraesentiert eine Stadt als Organisationseinheit, unterhalb
 * derer einzelne Critical-Mass-Touren stattfinden.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CityRepository")
 * @Vich\Uploadable
 * @ORM\Table(name="city")
 * @JMS\ExclusionPolicy("all")
 */
class City implements BoardInterface, ViewableInterface, ElasticSearchPinInterface, ArchiveableInterface
{
    /**
     * Numerische ID der Stadt.
     *
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
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="cities")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="CitySlug", inversedBy="cities")
     * @ORM\JoinColumn(name="main_slug_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $mainSlug;

    /**
     * Name der Stadt.
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\SerializedName("name")
     * @JMS\Groups({"ride-list"})
     */
    protected $city;

    /**
     * Bezeichnung der Critical Mass in dieser Stadt, etwa "Critical Mass Hamburg"
     * oder "Critical Mass Bremen".
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $title;

    /**
     * Kurze Beschreibung der Critical Mass dieser Stadt.
     *
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $description;

    /**
     * Adresse der Webseite der Critical Mass in dieser Stadt.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $url;

    /**
     * Adresse der Critical-Mass-Seite auf facebook dieser Stadt.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $facebook;

    /**
     * Adresse der Twitter-Seite der Critical Mass dieser Stadt.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $twitter;

    /**
     * Breitengrad der Stadt.
     *
     * @ORM\Column(type="float")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $latitude = 0;

    /**
     * Längengrad der Stadt.
     *
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
     * Array mit den Touren in dieser Stadt.
     *
     * @ORM\OneToMany(targetEntity="Ride", mappedBy="city")
     */
    protected $rides;

    /**
     * Array mit den Kommentaren zu dieser Stadt.
     *
     * @ORM\OneToMany(targetEntity="Post", mappedBy="city")
     */
    protected $posts;

    /**
     * Array mit den Bildern zu dieser Stadt.
     *
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
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     */
    protected $isStandardable = false;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Expose
     */
    protected $standardDayOfWeek;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Expose
     */
    protected $standardWeekOfMonth;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     */
    protected $isStandardableTime = false;

    /**
     * @ORM\Column(type="time", nullable=true)
     * @JMS\Expose
     */
    protected $standardTime;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     */
    protected $isStandardableLocation = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose
     */
    protected $standardLocation;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $standardLatitude = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $standardLongitude = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="int")
     * @JMS\Expose
     */
    protected $cityPopulation = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose
     */
    protected $punchLine;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $longDescription;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="archive_cities")
     * @ORM\JoinColumn(name="archive_parent_id", referencedColumnName="id")
     */
    protected $archiveParent;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isArchived = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $archiveDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="archive_rides")
     * @ORM\JoinColumn(name="archive_user_id", referencedColumnName="id")
     */
    protected $archiveUser;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $archiveMessage;

    /**
     * @Vich\UploadableField(mapping="city_photo", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

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
    protected $enableBoard;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $timezone;

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

    public function __construct()
    {
        $this->rides = new ArrayCollection();
        $this->slugs = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->photos = new ArrayCollection();

        $this->archiveDateTime = new \DateTime();
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

    public function setUser(User $user): City
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
     */
    public function getMainSlugString(): string
    {
        return $this->getMainSlug()->getSlug();
    }

    public function getSlug(): string
    {
        return $this->getMainSlugString();
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

    public function getCity(): string
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

    public function setUrl(string $url): City
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setFacebook(string $facebook): City
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setTwitter(string $twitter): City
    {
        $this->twitter = $twitter;

        return $this;
    }

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

    /** @deprecated  */
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

    public function setIsStandardable(bool $isStandardable): City
    {
        $this->isStandardable = $isStandardable;

        return $this;
    }

    public function getIsStandardable(): bool
    {
        return $this->isStandardable;
    }

    public function setStandardDayOfWeek(int $standardDayOfWeek): City
    {
        $this->standardDayOfWeek = $standardDayOfWeek;

        return $this;
    }

    public function getStandardDayOfWeek(): int
    {
        return $this->standardDayOfWeek;
    }

    public function setStandardWeekOfMonth(int $standardWeekOfMonth): City
    {
        $this->standardWeekOfMonth = $standardWeekOfMonth;

        return $this;
    }

    public function getStandardWeekOfMonth(): int
    {
        return $this->standardWeekOfMonth;
    }

    public function setStandardTime(\DateTime $standardTime): City
    {
        $this->standardTime = $standardTime;

        return $this;
    }

    public function getStandardTime(): \DateTime
    {
        return $this->standardTime;
    }

    public function setStandardLocation(string $standardLocation = null): City
    {
        $this->standardLocation = $standardLocation;

        return $this;
    }

    public function getStandardLocation(): ?string
    {
        return $this->standardLocation;
    }

    public function setStandardLatitude(float $standardLatitude = null): City
    {
        $this->standardLatitude = $standardLatitude;

        return $this;
    }

    public function getStandardLatitude(): ?float
    {
        return $this->standardLatitude;
    }

    public function setStandardLongitude(float $standardLongitude = null): City
    {
        $this->standardLongitude = $standardLongitude;

        return $this;
    }

    public function getStandardLongitude(): ?float
    {
        return $this->standardLongitude;
    }

    /**
     * @deprecated
     */
    public function getEventDateTimeLocationString(): string
    {
        $result = $this->getEventDateTimeString();

        if ($this->standardLocation) {
            $result .= ': ' . $this->standardLocation;
        }

        return $result;
    }

    /**
     * @deprecated
     */
    public function getEventDateTimeString(): string
    {
        $weekDays = array(1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Sonnabend', 0 => 'Sonntag');
        $monthWeeks = array(1 => 'ersten', 2 => 'zweiten', 3 => 'dritten', 4 => 'vierten', 0 => 'letzten');

        $result = '';

        if ($this->isStandardable) {
            $result = 'jeweils am ' . $monthWeeks[$this->standardWeekOfMonth] . ' ' . $weekDays[$this->standardDayOfWeek];

            if ($this->standardTime) {
                $this->standardTime->setTimezone(new \DateTimeZone('UTC'));
                $result .= ' um ' . $this->standardTime->format('H.i') . ' Uhr';
            }
        }

        return $result;
    }

    public function setCityPopulation(int $cityPopulation): City
    {
        $this->cityPopulation = $cityPopulation;

        return $this;
    }

    public function getCityPopulation(): int
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
        return count($this->getActiveRides());
    }

    /**
     * @deprecated
     */
    public function getActiveRides(): array
    {
        $result = array();

        foreach ($this->rides as $ride) {
            if (!$ride->getIsArchived()) {
                $result[] = $ride;
            }
        }

        return $result;
    }

    /**
     * @deprecated
     */
    public function getCurrentRide(): ?Ride
    {
        $currentRide = null;
        $dateTime = new \DateTime();

        foreach ($this->getRides() as $ride) {
            if ($ride && !$currentRide && $ride->getIsArchived() == 0 && $ride->getDateTime() > $dateTime) {
                $currentRide = $ride;
            } elseif ($ride && $currentRide && $ride->getIsArchived() == 0 && $ride->getDateTime() < $currentRide->getDateTime() && $ride->getDateTime() > $dateTime) {
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

    public function setIsArchived(bool $isArchived): ArchiveableInterface
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getIsArchived(): bool
    {
        return $this->isArchived;
    }

    public function setArchiveDateTime(\DateTime $archiveDateTime): ArchiveableInterface
    {
        $this->archiveDateTime = $archiveDateTime;

        return $this;
    }

    public function getArchiveDateTime(): \DateTime
    {
        return $this->archiveDateTime;
    }

    public function setArchiveParent(ArchiveableInterface $archiveParent): ArchiveableInterface
    {
        $this->archiveParent = $archiveParent;

        return $this;
    }

    public function getArchiveParent(): ArchiveableInterface
    {
        return $this->archiveParent;
    }

    public function setArchiveUser(User $archiveUser): ArchiveableInterface
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    public function getArchiveUser(): User
    {
        return $this->archiveUser;
    }

    public function setArchiveMessage(string $archiveMessage): ArchiveableInterface
    {
        $this->archiveMessage = $archiveMessage;

        return $this;
    }

    public function getArchiveMessage(): string
    {
        return $this->archiveMessage;
    }

    public function __clone()
    {
        $this->id = null;
        $this->setIsArchived(true);
        $this->setArchiveDateTime(new \DateTime());
    }

    public function setIsStandardableLocation(bool $isStandardableLocation): City
    {
        $this->isStandardableLocation = $isStandardableLocation;

        return $this;
    }

    public function getIsStandardableLocation(): bool
    {
        return $this->isStandardableLocation;
    }

    public function setIsStandardableTime(bool $isStandardableTime): City
    {
        $this->isStandardableTime = $isStandardableTime;

        return $this;
    }

    public function getIsStandardableTime(): bool
    {
        return $this->isStandardableTime;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function setImageFile(File $image = null): City
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

    public function setImageName(string $imageName): City
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getPin(): string
    {
        return sprintf('%f,%f', $this->latitude, $this->longitude);
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

    public function getTimezone(): string
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

    public function archive(User $user): ArchiveableInterface
    {
        $archivedCity = clone $this;

        $archivedCity
            ->setIsArchived(true)
            ->setArchiveDateTime(new \DateTime())
            ->setArchiveParent($this)
            ->setArchiveUser($user)
            ->setArchiveMessage($this->archiveMessage)
            ->setImageFile(null)
        ;

        $this->archiveMessage = '';

        return $archivedCity;
    }
}
