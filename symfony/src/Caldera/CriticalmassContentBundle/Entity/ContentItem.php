<?php

namespace Caldera\CriticalmassContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_item")
 */
class ContentItem
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ContentClass", inversedBy="content_items")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $contentClass;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="integer")
     */
    protected $positionOrder;
 

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
     * @return ContentItem
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
     * Set text
     *
     * @param string $text
     * @return ContentItem
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return ContentItem
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set positionOrder
     *
     * @param integer $positionOrder
     * @return ContentItem
     */
    public function setPositionOrder($positionOrder)
    {
        $this->positionOrder = $positionOrder;

        return $this;
    }

    /**
     * Get positionOrder
     *
     * @return integer 
     */
    public function getPositionOrder()
    {
        return $this->positionOrder;
    }

    /**
     * Set helpClass
     *
     * @param \Caldera\CriticalmassContentBundle\Entity\ContentClass $helpClass
     * @return ContentItem
     */
    public function setContentClass(\Caldera\CriticalmassContentBundle\Entity\ContentClass $contentClass = null)
    {
        $this->contentClass = $contentClass;

        return $this;
    }

    /**
     * Get helpClass
     *
     * @return \Caldera\CriticalmassContentBundle\Entity\ContentClass
     */
    public function getContentClass()
    {
        return $this->contentClass;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return ContentItem
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function getParsedText()
    {
        $text = $this->getText();

        $text = str_replace(array("&lt;", "&gt;", "&quot;"), array("<", ">", "\""), $text);

        return $text;
    }
}
