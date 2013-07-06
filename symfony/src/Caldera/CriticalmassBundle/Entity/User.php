<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $sendGPSInformation = 1;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $gpsInterval = 10;

	/**
	 * @ORM\OneToOne(targetEntity="City")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
	private $currentCity;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Hasht die E-Mail-Adresse per MD5, um das dazugehörige Gravartar-Profilbild
	 * aufrufen zu können.
	 *
	 * @return String MD5-gehashte E-Mail-Adresse
	 */
	public function getGravatarHash()
	{
		return md5($this->getEmail());
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
     * Set sendGPSInformation
     *
     * @param boolean $sendGPSInformation
     * @return User
     */
    public function setSendGPSInformation($sendGPSInformation)
    {
        $this->sendGPSInformation = $sendGPSInformation;
    
        return $this;
    }

    /**
     * Get sendGPSInformation
     *
     * @return boolean 
     */
    public function getSendGPSInformation()
    {
        return $this->sendGPSInformation;
    }

    /**
     * Set currentCity
     *
     * @param \Caldera\CriticalmassBundle\Entity\City $currentCity
     * @return User
     */
    public function setCurrentCity(\Caldera\CriticalmassBundle\Entity\City $currentCity = null)
    {
        $this->currentCity = $currentCity;
    
        return $this;
    }

    /**
     * Get currentCity
     *
     * @return \Caldera\CriticalmassBundle\Entity\City 
     */
    public function getCurrentCity()
    {
        return $this->currentCity;
    }

    /**
     * Set gpsInterval
     *
     * @param integer $gpsInterval
     * @return User
     */
    public function setGpsInterval($gpsInterval)
    {
        $this->gpsInterval = $gpsInterval;
    
        return $this;
    }

    /**
     * Get gpsInterval
     *
     * @return integer 
     */
    public function getGpsInterval()
    {
        return $this->gpsInterval;
    }

		public function getCurrentCitySlug()
		{
			return $this->getCurrentCity()->getMainSlug()->getSlug();
		}
}