<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CriticalmassModelBundle\Repository\ContentRepository")
 */
class Content
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    protected $id;

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

    protected $formattedText;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isPublicEditable = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $showInfobox = true;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastEditionDateTime = true;
    
    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="archive_contents")
     * @ORM\JoinColumn(name="archive_parent_id", referencedColumnName="id")
     */
    protected $archiveParent;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isArchived = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $archiveDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="archive_rides")
     * @ORM\JoinColumn(name="archive_user_id", referencedColumnName="id")
     */
    protected $archiveUser;
    
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

    public function setFormattedText($formattedText)
    {
        $this->formattedText = $formattedText;

        return $this;
    }

    public function getFormattedText()
    {
        return $this->formattedText;
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

    public function __clone()
    {
        $this->id = null;
        $this->setIsArchived(true);
        $this->setArchiveDateTime(new \DateTime());
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return Content
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean 
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set archiveDateTime
     *
     * @param \DateTime $archiveDateTime
     * @return Content
     */
    public function setArchiveDateTime($archiveDateTime)
    {
        $this->archiveDateTime = $archiveDateTime;

        return $this;
    }

    /**
     * Get archiveDateTime
     *
     * @return \DateTime 
     */
    public function getArchiveDateTime()
    {
        return $this->archiveDateTime;
    }

    /**
     * Set archiveParent
     *
     * @param Content $archiveParent
     * @return Content
     */
    public function setArchiveParent(Content $archiveParent = null)
    {
        $this->archiveParent = $archiveParent;

        return $this;
    }

    /**
     * Get archiveParent
     *
     * @return Content
     */
    public function getArchiveParent()
    {
        return $this->archiveParent;
    }

    /**
     * Set archiveUser
     *
     * @param \Application\Sonata\UserBundle\Entity\User $archiveUser
     * @return Content
     */
    public function setArchiveUser(\Application\Sonata\UserBundle\Entity\User $archiveUser = null)
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    /**
     * Get archiveUser
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getArchiveUser()
    {
        return $this->archiveUser;
    }

    /**
     * Set isPublicEditable
     *
     * @param boolean $isPublicEditable
     * @return Content
     */
    public function setIsPublicEditable($isPublicEditable)
    {
        $this->isPublicEditable = $isPublicEditable;

        return $this;
    }

    /**
     * Get isPublicEditable
     *
     * @return boolean 
     */
    public function getIsPublicEditable()
    {
        return $this->isPublicEditable;
    }

    /**
     * Set lastEditionDateTime
     *
     * @param \DateTime $lastEditionDateTime
     * @return Content
     */
    public function setLastEditionDateTime($lastEditionDateTime)
    {
        $this->lastEditionDateTime = $lastEditionDateTime;

        return $this;
    }

    /**
     * Get lastEditionDateTime
     *
     * @return \DateTime 
     */
    public function getLastEditionDateTime()
    {
        return $this->lastEditionDateTime;
    }

    /**
     * Set showInfobox
     *
     * @param boolean $showInfobox
     * @return Content
     */
    public function setShowInfobox($showInfobox)
    {
        $this->showInfobox = $showInfobox;

        return $this;
    }

    /**
     * Get showInfobox
     *
     * @return boolean 
     */
    public function getShowInfobox()
    {
        return $this->showInfobox;
    }
}
