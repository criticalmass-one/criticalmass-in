<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diese Entitaet repraesentiert eine Stadt als Organisationseinheit, unterhalb
 * derer einzelne Critical-Mass-Touren stattfinden.
 *
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassBundle\Entity\CityRepository")
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
	 * Adresse der Webseite der Critical Mass in dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	protected $url;

	/**
	 * Adresse der Critical-Mass-Seite auf facebook dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	protected $facebook;

	/**
	 * Adresse der Twitter-Seite der Critical Mass dieser Stadt.
	 *
	 * @ORM\Column(type="string", length=255)
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
	 * Array mit den Touren in dieser Stadt.
	 *
	 * @ORM\OneToMany(targetEntity="Ride", mappedBy="city")
	 */
	protected $rides;

	/**
	 * @ORM\OneToMany(targetEntity="CitySlug", mappedBy="city")
	 */
	protected $slugs;

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
	 * @return Entity\CitySlug: Beliebiger Slug dieser Stadt
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
     * @param \Caldera\CriticalmassBundle\Entity\Ride $rides
     * @return City
     */
    public function addRide(\Caldera\CriticalmassBundle\Entity\Ride $rides)
    {
        $this->rides[] = $rides;
    
        return $this;
    }

    /**
     * Remove rides
     *
     * @param \Caldera\CriticalmassBundle\Entity\Ride $rides
     */
    public function removeRide(\Caldera\CriticalmassBundle\Entity\Ride $rides)
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
     * @param \Caldera\CriticalmassBundle\Entity\CitySlug $slugs
     * @return City
     */
    public function addSlug(\Caldera\CriticalmassBundle\Entity\CitySlug $slugs)
    {
        $this->slugs[] = $slugs;
    
        return $this;
    }

    /**
     * Remove slugs
     *
     * @param \Caldera\CriticalmassBundle\Entity\CitySlug $slugs
     */
    public function removeSlug(\Caldera\CriticalmassBundle\Entity\CitySlug $slugs)
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
}