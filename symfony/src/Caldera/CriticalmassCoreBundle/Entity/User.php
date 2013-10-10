<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Diese Benutzer-Entitaet basiert auf der User-Enitaet des FOSUserBundles und 
 * fuegt einige zusaetzliche Eigenschaften hinzu.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
	/**
	 * Numerische ID dieses Benutzers.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Schalter fuer die GPS-Aktivitaet, entscheidet ob der Benutzer GPS-Daten von
	 * der Live-Uebersicht senden moechte.
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $sendGPSInformation = 1;

	/**
	 * Vom Benutzer gewaehltes Intervall in Sekunden, in dem GPS-Daten an den
	 * Server gesendet werden sollen.
	 *
	 * @ORM\Column(type="integer")
	 */
	private $gpsInterval = 10;

	/**
	 * Vom Benutzer momentan ausgewaehlte Stadt.
	 *
	 * @ORM\ManyToOne(targetEntity="City")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
	private $currentCity;

	/**
	 * Benutzer-Token des Pushover-Dienstes, an den Push-Nachrichten adressiert
	 * werden koennen.
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $pushoverKey = '';

	/**
	 * Der Konstruktor-Aufruf wird direkt an das FOSUserBundle deligiert.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Hasht die E-Mail-Adresse per MD5, um das dazugehörige Gravartar-Profilbild
	 * aufrufen zu können.
	 *
	 * @return String: MD5-gehashte E-Mail-Adresse
	 */
	public function getGravatarHash()
	{
		return md5($this->getEmail());
	}

	/**
	 * Gibt den Slug der Stadt zurueck, die der Benutzer gerade ausgewaehlt hat.
	 * Hilfreich, um beispielsweise innerhalb eines Templates automatisch einen
	 * Slug angeben zu koennen, um Routen zu konstruieren.
	 *
	 * @return String: Slug der ausgewaehlten Stadt
	 */
	public function getCurrentCitySlug()
	{
		return $this->getCurrentCity()->getMainSlug()->getSlug();
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
     * @param \Caldera\CriticalmassCoreBundle\Entity\City $currentCity
     * @return User
     */
    public function setCurrentCity(\Caldera\CriticalmassCoreBundle\Entity\City $currentCity = null)
    {
        $this->currentCity = $currentCity;
    
        return $this;
    }

    /**
     * Get currentCity
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\City
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

    /**
     * Set pushoverKey
     *
     * @param string $pushoverKey
     * @return User
     */
    public function setPushoverKey($pushoverKey)
    {
        $this->pushoverKey = $pushoverKey;
    
        return $this;
    }

    /**
     * Get pushoverKey
     *
     * @return string 
     */
    public function getPushoverKey()
    {
        return $this->pushoverKey;
    }
}
