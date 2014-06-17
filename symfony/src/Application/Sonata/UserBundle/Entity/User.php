<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
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
     * Enthaelt eine kurze Beschreibung zur eigenen Person.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * Vom Benutzer momentan ausgewaehlte Stadt.
     *
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $currentCity;

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
     * @ORM\Column(type="string", length=32)
     */
    protected $pingToken;

    /**
     * Der Konstruktor-Aufruf wird direkt an das FOSUserBundle deligiert.
     */
    public function __construct()
    {
        parent::__construct();

        $this->pingToken = md5(microtime());
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
     * Set description
     *
     * @param string $description
     * @return User
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

    public function getColorRed()
    {
        return $this->colorRed;
    }

    public function setColorRed($colorRed)
    {
        $this->colorRed = $colorRed;
    }

    public function getColorGreen()
    {
        return $this->colorGreen;
    }

    public function setColorGreen($colorGreen)
    {
        $this->colorGreen = $colorGreen;
    }

    public function getColorBlue()
    {
        return $this->colorBlue;
    }

    public function setColorBlue($colorBlue)
    {
        $this->colorBlue = $colorBlue;
    }

    public function setPingToken($pingToken)
    {
        $this->pingToken = $pingToken;
    }

    public function getPingToken()
    {
        return $this->pingToken;
    }
}