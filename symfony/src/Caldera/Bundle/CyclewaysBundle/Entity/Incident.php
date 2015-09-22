<?php

namespace Caldera\Bundle\CyclewaysBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Caldera\Bundle\CyclewaysBundle\Entity\Repository\IncidentRepository")
 * @ORM\Table(name="cycleways_incident")
 */
class Incident
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="incidents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="IncidentType", inversedBy="incidents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $type;

    /**
	 * @ORM\Column(type="float")
	 */
	protected $latitude;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $longitude;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $creationDateTime;

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
     * Set latitude
     *
     * @param float $latitude
     * @return Position
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
     * @return Position
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
     * Set user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     * @return Position
     */
    public function setUser(\Application\Sonata\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Position
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime 
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * Set type
     *
     * @param \Caldera\Bundle\CyclewaysBundle\Entity\IncidentType $type
     * @return Incident
     */
    public function setType(\Caldera\Bundle\CyclewaysBundle\Entity\IncidentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Caldera\Bundle\CyclewaysBundle\Entity\IncidentType 
     */
    public function getType()
    {
        return $this->type;
    }
}
