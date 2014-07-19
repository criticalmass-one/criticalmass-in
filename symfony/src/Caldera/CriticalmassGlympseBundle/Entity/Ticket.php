<?php

namespace Caldera\CriticalmassGlympseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="glympse_ticket")
 */
class Ticket
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\City", inversedBy="tickets")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

	/**
	 * @ORM\Column(type="string", length=9, nullable=false)
	 */
	protected $inviteId;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $creationDateTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $runtime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $counter;

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
     * Set inviteId
     *
     * @param string $inviteId
     * @return Ticket
     */
    public function setInviteId($inviteId)
    {
        $this->inviteId = $inviteId;

        return $this;
    }

    /**
     * Get inviteId
     *
     * @return string 
     */
    public function getInviteId()
    {
        return $this->inviteId;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Ticket
     */
    public function setCreationDateTime(\DateTime $creationDateTime)
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
     * Set runtime
     *
     * @param integer $runtime
     * @return Ticket
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;

        return $this;
    }

    /**
     * Get runtime
     *
     * @return integer 
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * Set city
     *
     * @param \Caldera\CriticalmassCoreBundle\Entity\City $city
     * @return Ticket
     */
    public function setCity(\Caldera\CriticalmassCoreBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\CriticalmassCoreBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set counter
     *
     * @param integer $counter
     * @return Ticket
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer 
     */
    public function getCounter()
    {
        return $this->counter;
    }
}
