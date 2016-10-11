<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\IncidentRepository")
 * @ORM\Table(name="incident")
 * @JMS\ExclusionPolicy("all")
 */
class Incident
{
    const INCIDENT_RAGE = 'Road Rage';
    const INCIDENT_ROADWORKS = 'Arbeitsstelle';
    const INCIDENT_DANGER = 'Gefahrenstelle';
    const INCIDENT_POLICE = 'Polizeikontrolle';
    const INCIDENT_ACCIDENT = 'Unfall';
    const INCIDENT_DEADLY_ACCIDENT = 'Tödlicher Unfall';

    const GEOMETRY_POLYLINE = 'Polyline';
    const GEOMETRY_MARKER = 'Marker';

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
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
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
     * @ORM\Column(type="text")
     * @JMS\Expose
     */
    protected $polyline;

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
}
