<?php

namespace Caldera\Bundle\CyclewaysBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cycleways_incident_type")
 */
class IncidentType
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $caption;

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
     * @return IncidentType
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getCaption()
    {
        return $this->caption;
    }
}
