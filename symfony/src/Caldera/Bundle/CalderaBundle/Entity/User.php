<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * Numerische ID dieses Benutzers.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $username;

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
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $colorRed = 0;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $colorGreen = 0;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
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
    protected $phoneNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $phoneNumberVerified;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $phoneNumberVerificationDateTime;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $phoneNumberVerificationToken;

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
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("gravatarHash")
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

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set phoneNumberVerified
     *
     * @param boolean $phoneNumberVerified
     * @return User
     */
    public function setPhoneNumberVerified($phoneNumberVerified)
    {
        $this->phoneNumberVerified = $phoneNumberVerified;

        return $this;
    }

    /**
     * Get phoneNumberVerified
     *
     * @return boolean 
     */
    public function getPhoneNumberVerified()
    {
        return $this->phoneNumberVerified;
    }

    /**
     * Set phoneNumberVerificationDateTime
     *
     * @param \DateTime $phoneNumberVerificationDateTime
     * @return User
     */
    public function setPhoneNumberVerificationDateTime($phoneNumberVerificationDateTime)
    {
        $this->phoneNumberVerificationDateTime = $phoneNumberVerificationDateTime;

        return $this;
    }

    /**
     * Get phoneNumberVerificationDateTime
     *
     * @return \DateTime 
     */
    public function getPhoneNumberVerificationDateTime()
    {
        return $this->phoneNumberVerificationDateTime;
    }

    /**
     * Set phoneNumberVerificationToken
     *
     * @param string $phoneNumberVerificationToken
     * @return User
     */
    public function setPhoneNumberVerificationToken($phoneNumberVerificationToken)
    {
        $this->phoneNumberVerificationToken = $phoneNumberVerificationToken;

        return $this;
    }

    /**
     * Get phoneNumberVerificationToken
     *
     * @return string
     */
    public function getPhoneNumberVerificationToken()
    {
        return $this->phoneNumberVerificationToken;
    }

    /**
     * Add archiveRides
     *
     * @param \Caldera\Bundle\CriticalmassModelBundle\Entity\Ride $archiveRides
     * @return User
     */
    public function addArchiveRide(\Caldera\Bundle\CriticalmassModelBundle\Entity\Ride $archiveRides)
    {
        $this->archiveRides[] = $archiveRides;

        return $this;
    }

    /**
     * Remove archiveRides
     *
     * @param \Caldera\Bundle\CriticalmassModelBundle\Entity\Ride $archiveRides
     */
    public function removeArchiveRide(\Caldera\Bundle\CriticalmassModelBundle\Entity\Ride $archiveRides)
    {
        $this->archiveRides->removeElement($archiveRides);
    }

    /**
     * Get archiveRides
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArchiveRides()
    {
        return $this->archiveRides;
    }

    /**
     * Add participations
     *
     * @param \Caldera\Bundle\CriticalmassModelBundle\Entity\Participation $participations
     * @return User
     */
    public function addParticipation(\Caldera\Bundle\CriticalmassModelBundle\Entity\Participation $participations)
    {
        $this->participations[] = $participations;

        return $this;
    }

    /**
     * Remove participations
     *
     * @param \Caldera\Bundle\CriticalmassModelBundle\Entity\Participation $participations
     */
    public function removeParticipation(\Caldera\Bundle\CriticalmassModelBundle\Entity\Participation $participations)
    {
        $this->participations->removeElement($participations);
    }

    /**
     * Get participations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipations()
    {
        return $this->participations;
    }
}
