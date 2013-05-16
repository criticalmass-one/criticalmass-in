<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="city")
 */
class City
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
  protected $id;

	/**
	 * @ORM\Column(type="string", length=50)
	 */
	protected $city;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $title;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $url;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $facebook;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $twitter;

	/**
	 * @ORM\OneToMany(targetEntity="Ride", mappedBy="city_id")
	 */
	protected $rides;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rides = new \Doctrine\Common\Collections\ArrayCollection();
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

		public function getCurrentRide()
		{
			$rides = $this->getRides();
			return $rides[count($rides) - 1];
		}
}