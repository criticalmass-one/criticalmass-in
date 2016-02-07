<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="criticalmaps_user")
 */
class CriticalmapsUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\Bundle\CriticalmassModelBundle\Entity\City", inversedBy="criticalmaps_users")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	protected $identifier;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $creationDateTime;

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
     * @ORM\Column(type="datetime")
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $endDateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $exported = false;

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
     * Set identifier
     *
     * @param string $identifier
     * @return CriticalmapsUser
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return CriticalmapsUser
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
     * Set colorRed
     *
     * @param integer $colorRed
     * @return CriticalmapsUser
     */
    public function setColorRed($colorRed)
    {
        $this->colorRed = $colorRed;

        return $this;
    }

    /**
     * Get colorRed
     *
     * @return integer 
     */
    public function getColorRed()
    {
        return $this->colorRed;
    }

    /**
     * Set colorGreen
     *
     * @param integer $colorGreen
     * @return CriticalmapsUser
     */
    public function setColorGreen($colorGreen)
    {
        $this->colorGreen = $colorGreen;

        return $this;
    }

    /**
     * Get colorGreen
     *
     * @return integer 
     */
    public function getColorGreen()
    {
        return $this->colorGreen;
    }

    /**
     * Set colorBlue
     *
     * @param integer $colorBlue
     * @return CriticalmapsUser
     */
    public function setColorBlue($colorBlue)
    {
        $this->colorBlue = $colorBlue;

        return $this;
    }

    /**
     * Get colorBlue
     *
     * @return integer 
     */
    public function getColorBlue()
    {
        return $this->colorBlue;
    }

    /**
     * Set startDateTime
     *
     * @param \DateTime $startDateTime
     * @return CriticalmapsUser
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    /**
     * Get startDateTime
     *
     * @return \DateTime 
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * Set endDateTime
     *
     * @param \DateTime $endDateTime
     * @return CriticalmapsUser
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    /**
     * Get endDateTime
     *
     * @return \DateTime 
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     * Set city
     *
     * @param \Caldera\Bundle\CriticalmassModelBundle\Entity\City $city
     * @return CriticalmapsUser
     */
    public function setCity(\Caldera\Bundle\CriticalmassModelBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\Bundle\CriticalmassModelBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    public function setExported($exported)
    {
        $this->exported = $exported;

        return $this;
    }

    public function getExported()
    {
        return $this->exported;
    }
}
