<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Diese Entitaet repraesentiert eine Stadt als Organisationseinheit, unterhalb
 * derer einzelne Critical-Mass-Touren stattfinden.
 *
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassCoreBundle\Entity\CityRepository")
 * @ORM\Table(name="city")
 */
class City
{
	/**
	 * Numerische ID der Stadt.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
  protected $id;

	/**
	 * Name der Stadt.
	 *
	 * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
	 */
	protected $city;

	/**
	 * Bezeichnung der Critical Mass in dieser Stadt, etwa "Critical Mass Hamburg"
	 * oder "Critical Mass Bremen".
	 *
	 * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
	 */
	protected $title;

    /**
     * Kurze Beschreibung der Critical Mass dieser Stadt.
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
	 * Adresse der Webseite der Critical Mass in dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
	 */
	protected $url;

	/**
	 * Adresse der Critical-Mass-Seite auf facebook dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
	 */
	protected $facebook;

	/**
	 * Adresse der Twitter-Seite der Critical Mass dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
	 */
	protected $twitter;

	/**
	 * Breitengrad der Stadt.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $latitude;

	/**
	 * Längengrad der Stadt.
	 *
	 * @ORM\Column(type="float")
	 */
	protected $longitude;

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
     * @ORM\OneToMany(targetEntity="Caldera\CriticalmassTimelineBundle\Entity\Post", mappedBy="city")
     */
    protected $posts;

	/**
	 * @ORM\OneToMany(targetEntity="CitySlug", mappedBy="city", cascade={"persist", "remove"})
	 */
	protected $slugs;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isStandardable = false;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $standardDayOfWeek;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $standardWeekOfMonth;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $standardTime;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $standardLocation;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $standardLatitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $standardLongitude;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $autoDetect = true;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $cityPopulation;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $punchLine;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $longDescription;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $colorRed = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $colorGreen = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $colorBlue = 0;
    
    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="archive_cities")
     * @ORM\JoinColumn(name="archive_parent_id", referencedColumnName="id")
     */
    protected $archiveParent;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isArchived;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $archiveDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="archive_rides")
     * @ORM\JoinColumn(name="archive_user_id", referencedColumnName="id")
     */
    protected $archiveUser;

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
	 * Diese Methode gibt den ersten Slug dieser Stadt zurueck, mit dem unter an-
	 * derem Verlinkungen innerhalb der Web-App-Routen konstruiert werden.
	 *
	 * @return CitySlug: Beliebiger Slug dieser Stadt
	 */
	public function getMainSlug()
	{
		return $this->slugs[0];
	}

    public function getMainSlugString()
    {
        return $this->getMainSlug()->getSlug();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rides = new \Doctrine\Common\Collections\ArrayCollection();
        $this->slugs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Caldera\CriticalmassCoreBundle\Entity\Ride $rides
     * @return City
     */
    public function addRide(\Caldera\CriticalmassCoreBundle\Entity\Ride $rides)
    {
        $this->rides[] = $rides;
    
        return $this;
    }

    /**
     * Remove rides
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\Ride $rides
     */
    public function removeRide(\Caldera\CriticalmassCoreBundle\Entity\Ride $rides)
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
     * Add slugs
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\CitySlug $slugs
     * @return City
     */
    public function addSlug(\Caldera\CriticalmassCoreBundle\Entity\CitySlug $slugs)
    {
        $this->slugs[] = $slugs;
    
        return $this;
    }

    /**
     * Remove slugs
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\CitySlug $slugs
     */
    public function removeSlug(\Caldera\CriticalmassCoreBundle\Entity\CitySlug $slugs)
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

        if ($this->standardLocation)
        {
            $result.= ': '.$this->standardLocation;
        }

        return $result;
    }

    public function getEventDateTimeString()
    {
        $weekDays = array(1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Sonnabend', 0 => 'Sonntag');
        $monthWeeks = array(1 => 'ersten', 2 => 'zweiten', 3 => 'dritten', 4 => 'vierten', 0 => 'letzten');

        $result = '';

        if ($this->isStandardable)
        {
            $result = 'jeweils am '.$monthWeeks[$this->standardWeekOfMonth].' '.$weekDays[$this->standardDayOfWeek];

            if ($this->standardTime)
            {
                $result.= ' um '.$this->standardTime->format('H.i').' Uhr';
            }
        }

        return $result;
    }

    /**
     * Set autoDetect
     *
     * @param boolean $autoDetect
     * @return City
     */
    public function setAutoDetect($autoDetect)
    {
        $this->autoDetect = $autoDetect;

        return $this;
    }

    /**
     * Get autoDetect
     *
     * @return boolean 
     */
    public function getAutoDetect()
    {
        return $this->autoDetect;
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
        return count($this->rides);
    }

    public function getCurrentRide()
    {
        $currentRide = null;
        $dateTime = new \DateTime();

        foreach ($this->getRides() as $ride)
        {
            if ($ride && !$currentRide && $ride->getDateTime() > $dateTime)
            {
                $currentRide = $ride;
            }
            else
            if ($ride && $currentRide && $ride->getDateTime() < $currentRide->getDateTime() && $ride->getDateTime() > $dateTime)
            {
                $currentRide = $ride;
            }
        }

        return $currentRide;
    }

    /**
     * Add posts
     *
     * @param \Caldera\CriticalmassTimelineBundle\Entity\Post $posts
     * @return City
     */
    public function addPost(\Caldera\CriticalmassTimelineBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Caldera\CriticalmassTimelineBundle\Entity\Post $posts
     */
    public function removePost(\Caldera\CriticalmassTimelineBundle\Entity\Post $posts)
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
    public function setIsArchived($isArchived)
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
    public function setArchiveDateTime($archiveDateTime)
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
     * @param \Caldera\CriticalmassCoreBundle\Entity\City $archiveParent
     * @return City
     */
    public function setArchiveParent(\Caldera\CriticalmassCoreBundle\Entity\City $archiveParent = null)
    {
        $this->archiveParent = $archiveParent;

        return $this;
    }

    /**
     * Get archiveParent
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\City
     */
    public function getArchiveParent()
    {
        return $this->archiveParent;
    }

    /**
     * Set archiveUser
     *
     * @param \Application\Sonata\UserBundle\Entity\User $archiveUser
     * @return City
     */
    public function setArchiveUser(\Application\Sonata\UserBundle\Entity\User $archiveUser = null)
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    /**
     * Get archiveUser
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getArchiveUser()
    {
        return $this->archiveUser;
    }

    public function __clone()
    {
        $this->id = null;
        $this->setIsArchived(true);
        $this->setArchiveDateTime(new \DateTime());
    }

    public function calculateAverageRideParticipants()
    {
        if (!count($this->rides))
        {
            return 0;
        }

        $participants = 0;
        $rideCounter = 0;

        foreach ($this->getRides() as $ride)
        {
            if ($ride->getEstimatedParticipants() > 0)
            {
                ++$rideCounter;
                $participants += $ride->getEstimatedParticipants();
            }
        }

        return $participants / $rideCounter;
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
}
