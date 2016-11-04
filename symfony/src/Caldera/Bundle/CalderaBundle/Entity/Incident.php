<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\CoordinateInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ElasticSearchPinInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\IncidentRepository")
 * @ORM\Table(name="incident")
 * @JMS\ExclusionPolicy("all")
 */
class Incident implements CoordinateInterface, ElasticSearchPinInterface
{
    const INCIDENT_RAGE = 'rage';
    const INCIDENT_ROADWORKS = 'roadworks';
    const INCIDENT_DANGER = 'danger';
    const INCIDENT_POLICE = 'police';
    const INCIDENT_ACCIDENT = 'accident';
    const INCIDENT_DEADLY_ACCIDENT = 'deadly_accident';
    const INCIDENT_INFRASTRUCTURE = 'infrastructure';

    const DANGER_LEVEL_NONE = 'none';
    const DANGER_LEVEL_LOW = 'low';
    const DANGER_LEVEL_NORMAL = 'normal';
    const DANGER_LEVEL_HIGH = 'high';

    const GEOMETRY_POLYLINE = 'polyline';
    const GEOMETRY_MARKER = 'marker';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="incidents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="incidents")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @JMS\Expose
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    protected $geometryType;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    protected $incidentType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $dangerLevel;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $street;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @JMS\Expose
     */
    protected $houseNumber;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @JMS\Expose
     */
    protected $zipCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $suburb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $district;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $polyline;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $latitude = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $longitude = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
     */
    protected $dateTime;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     */
    protected $expires;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $visibleFrom;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $visibleTo;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $permalink;

    public function __construct()
    {
        $dateInterval = new \DateInterval('P3M');

        $this->visibleFrom = new \DateTime();
        $this->visibleTo = new \DateTime();

        $this->visibleTo->add($dateInterval);

        $this->creationDateTime = new \DateTime();
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
     * Set title
     *
     * @param string $title
     * @return Incident
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

    public function setSlug(string $slug): Incident
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Incident
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

    /**
     * Set incidentType
     *
     * @param string $incidentType
     * @return Incident
     */
    public function setIncidentType($incidentType)
    {
        $this->incidentType = $incidentType;

        return $this;
    }

    /**
     * Get incidentType
     *
     * @return string
     */
    public function getIncidentType()
    {
        return $this->incidentType;
    }

    /**
     * Set geometryType
     *
     * @param string $type
     * @return Incident
     */
    public function setGeometryType($geometryType)
    {
        $this->geometryType = $geometryType;

        return $this;
    }

    /**
     * Get geometryType
     *
     * @return string
     */
    public function getGeometryType()
    {
        return $this->geometryType;
    }

    /**
     * Set polyline
     *
     * @param string $polyline
     * @return Incident
     */
    public function setPolyline($polyline)
    {
        $this->polyline = $polyline;

        return $this;
    }

    /**
     * Get polyline
     *
     * @return string
     */
    public function getPolyline()
    {
        return $this->polyline;
    }

    public function setAddress(string $address): Incident
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setDangerLevel(string $dangerLevel): Incident
    {
        $this->dangerLevel = $dangerLevel;

        return $this;
    }

    public function getDangerLevel()
    {
        return $this->dangerLevel;
    }

    public function setDateTime(\DateTime $dateTime): Incident
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set expires
     *
     * @param boolean $expires
     * @return Incident
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return boolean
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set visibleFrom
     *
     * @param \DateTime $visibleFrom
     * @return Incident
     */
    public function setVisibleFrom($visibleFrom)
    {
        $this->visibleFrom = $visibleFrom;

        return $this;
    }

    /**
     * Get visibleFrom
     *
     * @return \DateTime
     */
    public function getVisibleFrom()
    {
        return $this->visibleFrom;
    }

    /**
     * Set visibleTo
     *
     * @param \DateTime $visibleTo
     * @return Incident
     */
    public function setVisibleTo($visibleTo)
    {
        $this->visibleTo = $visibleTo;

        return $this;
    }

    /**
     * Get visibleTo
     *
     * @return \DateTime
     */
    public function getVisibleTo()
    {
        return $this->visibleTo;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Incident
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Caldera\Bundle\CalderaBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set city
     *
     * @param City $city
     * @return Incident
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    public function setCreationDateTime(\DateTime $creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function getPin(): string
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ',' . $this->longitude;
        }

        return '0,0';
    }

    public function indexable(): bool
    {
        if (!$this->latitude || !$this->longitude) {
            echo $this->latitude." ".$this->longitude."\n";
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet(string $street): Incident
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * @param mixed $houseNumber
     */
    public function setHouseNumber($houseNumber): Incident
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode): Incident
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuburb()
    {
        return $this->suburb;
    }

    /**
     * @param mixed $suburb
     */
    public function setSuburb($suburb): Incident
    {
        $this->suburb = $suburb;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district): Incident
    {
        $this->district = $district;

        return $this;
    }
    
    public function getPermalink()
    {
        return $this->permalink;
    }
    
    public function setPermalink(string $permalink): Incident
    {
        $this->permalink = $permalink;
        
        return $this;
    }
}
