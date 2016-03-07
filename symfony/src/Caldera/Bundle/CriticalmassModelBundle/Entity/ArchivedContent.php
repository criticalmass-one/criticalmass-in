<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AssociationOverrides;
use Doctrine\ORM\Mapping\AssociationOverride;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_archived")
 */
class ArchivedContent extends Content
{

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="archive_contents")
     * @ORM\JoinColumn(name="archive_parent_id", referencedColumnName="id")
     */
    protected $archiveParent;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $archiveDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="archive_contents")
     * @ORM\JoinColumn(name="archive_user_id", referencedColumnName="id")
     */
    protected $archiveUser;

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
     * @param User $archiveUser
     * @return Content
     */
    public function setArchiveUser(User $archiveUser = null)
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    /**
     * Get archiveUser
     *
     * @return User
     */
    public function getArchiveUser()
    {
        return $this->archiveUser;
    }

    public function cloneFrom(Content $content)
    {
        $this->setSlug($content->getSlug());
        $this->setTitle($content->getTitle());
        $this->setText($content->getText());
        $this->setEnabled($content->getEnabled());
        $this->setIsPublicEditable($content->getIsPublicEditable());
        $this->setShowInfobox($content->getShowInfobox());
        $this->setLastEditionDateTime($content->getLastEditionDateTime());
        $this->user2 = $content->getUser();

        $this->archiveParent2 = $content;
        $this->setArchiveDateTime(new \DateTime());

        return $this;
    }
}
