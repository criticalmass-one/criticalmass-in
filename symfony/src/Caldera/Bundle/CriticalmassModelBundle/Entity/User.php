<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\ManyToOne(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $currentCity;

    /**
     * @ORM\OneToMany(targetEntity="Track", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $tracks;

    /**
     * @ORM\OneToMany(targetEntity="Ride", mappedBy="user")
     */
    protected $archiveRides;

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
    protected $token;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\Regex("/^00491(5|6|7)(\d+)$/")
     */
    protected $mobilePhoneNumber;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="user")
     */
    protected $participations;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * Der Konstruktor-Aufruf wird direkt an das FOSUserBundle deligiert.
     */
    public function __construct()
    {
        parent::__construct();

        $this->colorRed = rand(0, 255);
        $this->colorGreen = rand(0, 255);
        $this->colorBlue = rand(0, 255);

        $this->token = md5(microtime());
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
     * @param City $currentCity
     * @return User
     */
    public function setCurrentCity(City $currentCity = null)
    {
        $this->currentCity = $currentCity;

        return $this;
    }

    /**
     * Get currentCity
     *
     * @return City
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

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function equals(User $user)
    {
        return $user->getId() == $this->getId();
    }

    /**
     * Add tracks
     *
     * @param Track $tracks
     * @return User
     */
    public function addTrack(Track $tracks)
    {
        $this->tracks[] = $tracks;

        return $this;
    }

    /**
     * Remove tracks
     *
     * @param Track $tracks
     */
    public function removeTrack(Track $tracks)
    {
        $this->tracks->removeElement($tracks);
    }

    /**
     * Get tracks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTracks()
    {
        return $this->tracks;
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

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setMobilePhoneNumber($mobilePhoneNumber)
    {
        $this->mobilePhoneNumber = $mobilePhoneNumber;

        return $this;
    }

    public function getMobilePhoneNumber()
    {
        return $this->mobilePhoneNumber;
    }

    /**
     * @ORM\PrePersist()
     *
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Hook on pre-update operations
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
