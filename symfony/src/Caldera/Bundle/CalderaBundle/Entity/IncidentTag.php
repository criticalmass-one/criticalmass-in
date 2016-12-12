<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\ViewInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="incident_tag")
 * @ORM\Entity()
 */
class IncidentTag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Caldera\Bundle\CalderaBundle\Entity\Incident")
     * @ORM\JoinTable(name="incident_incident_tag",
     *      joinColumns={@ORM\JoinColumn(name="incident_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    protected $incidents;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(name="font_color", type="string", length=255, nullable=true)
     */
    protected $fontColor;

    /**
     * @ORM\Column(name="background_color", type="string", length=255, nullable=true)
     */
    protected $backgroundColor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->incidents = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
     * @return IncidentTag
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
     * Set fontColor
     *
     * @param string $fontColor
     *
     * @return IncidentTag
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;

        return $this;
    }

    /**
     * Get fontColor
     *
     * @return string
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }

    /**
     * Set backgroundColor
     *
     * @param string $backgroundColor
     *
     * @return IncidentTag
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * Get backgroundColor
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * Add incident
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Incident $incident
     *
     * @return IncidentTag
     */
    public function addIncident(\Caldera\Bundle\CalderaBundle\Entity\Incident $incident)
    {
        $this->incidents[] = $incident;

        return $this;
    }

    /**
     * Remove incident
     *
     * @param \Caldera\Bundle\CalderaBundle\Entity\Incident $incident
     */
    public function removeIncident(\Caldera\Bundle\CalderaBundle\Entity\Incident $incident)
    {
        $this->incidents->removeElement($incident);
    }

    /**
     * Get incidents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIncidents()
    {
        return $this->incidents;
    }
}
