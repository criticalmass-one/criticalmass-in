<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
	 */
	protected $city;

	/**
	 * Bezeichnung der Critical Mass in dieser Stadt, etwa "Critical Mass Hamburg"
	 * oder "Critical Mass Bremen".
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	protected $title;

    /**
     * Kurze Beschreibung der Critical Mass dieser Stadt.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
	 * Adresse der Webseite der Critical Mass in dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $url;

	/**
	 * Adresse der Critical-Mass-Seite auf facebook dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $facebook;

	/**
	 * Adresse der Twitter-Seite der Critical Mass dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
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

            if ($this->standardLocation)
            {
                $result.= ': '.$this->standardLocation;
            }
        }

        return $result;
    }
}
