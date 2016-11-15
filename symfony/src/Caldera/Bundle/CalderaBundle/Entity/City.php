<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\ArchiveableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\BoardInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ElasticSearchPinInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Diese Entitaet repraesentiert eine Stadt als Organisationseinheit, unterhalb
 * derer einzelne Critical-Mass-Touren stattfinden.
 *
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\CityRepository")
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
     */
    protected $mainSlug;

    /**
     * Name der Stadt.
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @JMS\Expose
     * @JMS\SerializedName("name")
     */
    protected $city;

    /**
     * Bezeichnung der Critical Mass in dieser Stadt, etwa "Critical Mass Hamburg"
     * oder "Critical Mass Bremen".
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    protected $title;

    /**
     * Kurze Beschreibung der Critical Mass dieser Stadt.
     *
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $description;

    /**
     * Adresse der Webseite der Critical Mass in dieser Stadt.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     */
    protected $url;

    /**
     * Adresse der Critical-Mass-Seite auf facebook dieser Stadt.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     */
    protected $facebook;

    /**
     * Adresse der Twitter-Seite der Critical Mass dieser Stadt.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Expose
     */
    protected $twitter;

    /**
     * Breitengrad der Stadt.
     *
     * @ORM\Column(type="float")
     * @JMS\Expose
     */
    protected $latitude = 0;

    /**
     * Längengrad der Stadt.
     *
     * @ORM\Column(type="float")
     * @JMS\Expose
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
     * @ORM\Column(type="text")
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

    public function setRegion(Region $region)
    {
        $this->region = $region;

        return $this;
    }

    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return CitySlug
     */
    public function getMainSlug()
    {
        return $this->mainSlug;
    }

    /**
     * @param CitySlug $citySlug
     */
    public function setMainSlug(CitySlug $citySlug)
    {
        $this->mainSlug = $citySlug;

        return $this;
    }

    /**
     * Die Umwandlung dieser Entitaet in einen String geschieht unter anderem in
     * automatisch konstruierten Auswahlfeldern. In dem Fall soll diese Entitaet
     * mit dem Namen ihrer Stadt dargestellt werden.
     *
     * @return String: Name der Stadt
     */
    public function __toString()
    {
        return $this->getCity();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("slug")
     * @JMS\Type("string")
     * @return string
     */
    public function getMainSlugString()
    {
        return $this->getMainSlug()->getSlug();
    }

    public function getSlug()
    {
        return $this->getMainSlugString();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rides = new \Doctrine\Common\Collections\ArrayCollection();
        $this->slugs = new \Doctrine\Common\Collections\ArrayCollection();

        $this->archiveDateTime = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return City
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return City
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return City
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return City
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return City
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return City
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return City
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Add rides
     *
     * @param Ride $rides
     * @return City
     */
    public function addRide(Ride $rides)
    {
        $this->rides[] = $rides;

        return $this;
    }

    /**
     * Remove rides
     *
     * @param Ride $rides
     */
    public function removeRide(Ride $rides)
    {
        $this->rides->removeElement($rides);
    }

    /**
     * Get rides
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRides()
    {
        return $this->rides;
    }

    /**
     * Add slug
     *
     * @param CitySlug $slug
     * @return City
     */
    public function addSlug(CitySlug $slug)
    {
        if (!$this->mainSlug) {
            $this->mainSlug = $slug;
        }

        $this->slugs[] = $slug;

        return $this;
    }

    /**
     * Remove slugs
     *
     * @param CitySlug $slugs
     */
    public function removeSlug(CitySlug $slugs)
    {
        $this->slugs->removeElement($slugs);
    }

    /**
     * Get slugs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSlugs()
    {
        return $this->slugs;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return City
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function isEqual(City $city)
    {
        return $city->getId() == $this->getId();
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return City
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set isStandardable
     *
     * @param boolean $isStandardable
     * @return City
     */
    public function setIsStandardable($isStandardable)
    {
        $this->isStandardable = $isStandardable;

        return $this;
    }

    /**
     * Get isStandardable
     *
     * @return boolean
     */
    public function getIsStandardable()
    {
        return $this->isStandardable;
    }

    /**
     * Set standardDayOfWeek
     *
     * @param integer $standardDayOfWeek
     * @return City
     */
    public function setStandardDayOfWeek($standardDayOfWeek)
    {
        $this->standardDayOfWeek = $standardDayOfWeek;

        return $this;
    }

    /**
     * Get standardDayOfWeek
     *
     * @return integer
     */
    public function getStandardDayOfWeek()
    {
        return $this->standardDayOfWeek;
    }

    /**
     * Set standardWeekOfMonth
     *
     * @param integer $standardWeekOfMonth
     * @return City
     */
    public function setStandardWeekOfMonth($standardWeekOfMonth)
    {
        $this->standardWeekOfMonth = $standardWeekOfMonth;

        return $this;
    }

    /**
     * Get standardWeekOfMonth
     *
     * @return integer
     */
    public function getStandardWeekOfMonth()
    {
        return $this->standardWeekOfMonth;
    }

    /**
     * Set standardTime
     *
     * @param \DateTime $standardTime
     * @return City
     */
    public function setStandardTime($standardTime)
    {
        $this->standardTime = $standardTime;

        return $this;
    }

    /**
     * Get standardTime
     *
     * @return \DateTime
     */
    public function getStandardTime()
    {
        return $this->standardTime;
    }

    /**
     * Set standardLocation
     *
     * @param string $standardLocation
     * @return City
     */
    public function setStandardLocation($standardLocation)
    {
        $this->standardLocation = $standardLocation;

        return $this;
    }

    /**
     * Get standardLocation
     *
     * @return string
     */
    public function getStandardLocation()
    {
        return $this->standardLocation;
    }

    /**
     * Set standardLatitude
     *
     * @param float $standardLatitude
     * @return City
     */
    public function setStandardLatitude($standardLatitude)
    {
        $this->standardLatitude = $standardLatitude;

        return $this;
    }

    /**
     * Get standardLatitude
     *
     * @return float
     */
    public function getStandardLatitude()
    {
        return $this->standardLatitude;
    }

    /**
     * Set standardLongitude
     *
     * @param float $standardLongitude
     * @return City
     */
    public function setStandardLongitude($standardLongitude)
    {
        $this->standardLongitude = $standardLongitude;

        return $this;
    }

    /**
     * Get standardLongitude
     *
     * @return float
     */
    public function getStandardLongitude()
    {
        return $this->standardLongitude;
    }

    public function getEventDateTimeLocationString()
    {
        $result = $this->getEventDateTimeString();

        if ($this->standardLocation) {
            $result .= ': ' . $this->standardLocation;
        }

        return $result;
    }

    public function getEventDateTimeString()
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

    /**
     * Set cityPopulation
     *
     * @param integer $cityPopulation
     * @return City
     */
    public function setCityPopulation($cityPopulation)
    {
        $this->cityPopulation = $cityPopulation;

        return $this;
    }

    /**
     * Get cityPopulation
     *
     * @return integer
     */
    public function getCityPopulation()
    {
        return $this->cityPopulation;
    }

    /**
     * Set punchLine
     *
     * @param string $punchLine
     * @return City
     */
    public function setPunchLine($punchLine)
    {
        $this->punchLine = $punchLine;

        return $this;
    }

    /**
     * Get punchLine
     *
     * @return string
     */
    public function getPunchLine()
    {
        return $this->punchLine;
    }

    /**
     * Set longDescription
     *
     * @param string $longDescription
     * @return City
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * Get longDescription
     *
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    public function countRides()
    {
        return count($this->getActiveRides());
    }

    public function getActiveRides()
    {
        $result = array();

        foreach ($this->rides as $ride) {
            if (!$ride->getIsArchived()) {
                $result[] = $ride;
            }
        }

        return $result;
    }

    public function getCurrentRide()
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

    /**
     * Add posts
     *
     * @param Post $posts
     * @return City
     */
    public function addPost(Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param Post $posts
     */
    public function removePost(Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return City
     */
    public function setIsArchived(bool $isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set archiveDateTime
     *
     * @param \DateTime $archiveDateTime
     * @return City
     */
    public function setArchiveDateTime(\DateTime $archiveDateTime)
    {
        $this->archiveDateTime = $archiveDateTime;

        return $this;
    }

    /**
     * Get archiveDateTime
     *
     * @return \DateTime
     */
    public function getArchiveDateTime()
    {
        return $this->archiveDateTime;
    }

    /**
     * Set archiveParent
     *
     * @param City $archiveParent
     * @return City
     */
    public function setArchiveParent(ArchiveableInterface $archiveParent)
    {
        $this->archiveParent = $archiveParent;

        return $this;
    }

    /**
     * Get archiveParent
     *
     * @return City
     */
    public function getArchiveParent()
    {
        return $this->archiveParent;
    }

    /**
     * Set archiveUser
     *
     * @param User $archiveUser
     * @return City
     */
    public function setArchiveUser(User $archiveUser)
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    /**
     * Get archiveUser
     *
     * @return User
     */
    public function getArchiveUser()
    {
        return $this->archiveUser;
    }

    public function setArchiveMessage($archiveMessage)
    {
        $this->archiveMessage = $archiveMessage;

        return $this;
    }

    public function getArchiveMessage()
    {
        return $this->archiveMessage;
    }

    public function __clone()
    {
        $this->id = null;
        $this->setIsArchived(true);
        $this->setArchiveDateTime(new \DateTime());
    }

    /**
     * Set isStandardableLocation
     *
     * @param boolean $isStandardableLocation
     * @return City
     */
    public function setIsStandardableLocation($isStandardableLocation)
    {
        $this->isStandardableLocation = $isStandardableLocation;

        return $this;
    }

    /**
     * Get isStandardableLocation
     *
     * @return boolean
     */
    public function getIsStandardableLocation()
    {
        return $this->isStandardableLocation;
    }

    /**
     * Set isStandardableTime
     *
     * @param boolean $isStandardableTime
     * @return City
     */
    public function setIsStandardableTime($isStandardableTime)
    {
        $this->isStandardableTime = $isStandardableTime;

        return $this;
    }

    /**
     * Get isStandardableTime
     *
     * @return boolean
     */
    public function getIsStandardableTime()
    {
        return $this->isStandardableTime;
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    public function getPin(): string
    {
        return $this->latitude . ',' . $this->longitude;
    }

    public function setEnableBoard($enableBoard)
    {
        $this->enableBoard = $enableBoard;

        return $this;
    }

    public function getEnableBoard()
    {
        return $this->enableBoard;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setLastThread(Thread $lastThread)
    {
        $this->lastThread = $lastThread;

        return $this;
    }

    public function getLastThread()
    {
        return $this->lastThread;
    }

    public function setPostNumber($postNumber)
    {
        $this->postNumber = $postNumber;

        return $this;
    }

    public function getPostNumber()
    {
        return $this->postNumber;
    }

    public function incPostNumber()
    {
        ++$this->postNumber;
    }

    public function setThreadNumber($threadNumber)
    {
        $this->threadNumber = $threadNumber;

        return $this;
    }

    public function getThreadNumber()
    {
        return $this->threadNumber;
    }

    public function incThreadNumber()
    {
        ++$this->threadNumber;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return City
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set colorRed
     *
     * @param integer $colorRed
     * @return City
     */
    public function setColorRed($colorRed)
    {
        $this->colorRed = $colorRed;

        return $this;
    }

    /**
     * Get colorRed
     *
     * @return integer
     */
    public function getColorRed()
    {
        return $this->colorRed;
    }

    /**
     * Set colorGreen
     *
     * @param integer $colorGreen
     * @return City
     */
    public function setColorGreen($colorGreen)
    {
        $this->colorGreen = $colorGreen;

        return $this;
    }

    /**
     * Get colorGreen
     *
     * @return integer
     */
    public function getColorGreen()
    {
        return $this->colorGreen;
    }

    /**
     * Set colorBlue
     *
     * @param integer $colorBlue
     * @return City
     */
    public function setColorBlue($colorBlue)
    {
        $this->colorBlue = $colorBlue;

        return $this;
    }

    /**
     * Get colorBlue
     *
     * @return integer
     */
    public function getColorBlue()
    {
        return $this->colorBlue;
    }

    /**
     * Add photos
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Photo $photos
     * @return City
     */
    public function addPhoto(\Caldera\Bundle\CalderaBundle\Entity\Photo $photos)
    {
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Photo $photos
     */
    public function removePhoto(\Caldera\Bundle\CalderaBundle\Entity\Photo $photos)
    {
        $this->photos->removeElement($photos);
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function incViews()
    {
        ++$this->views;
    }

    public function getCountry()
    {
        if ($this->region) {
            return $this->region->getParent();
        }

        return null;
    }

    public function getDateTime()
    {
        return null;
    }
}
