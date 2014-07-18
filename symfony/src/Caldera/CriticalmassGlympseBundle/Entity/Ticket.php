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
}
