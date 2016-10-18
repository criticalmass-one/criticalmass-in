<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\BoardInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="board")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\BoardRepository")
 */
class Board implements BoardInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     */
    protected $threadNumber = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $postNumber = 0;

    /**
     * @ORM\OneToOne(targetEntity="Thread")
     * @ORM\JoinColumn(name="lastthread_id", referencedColumnName="id")
     */
    protected $lastThread;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
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
     * @return Post
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
     * @return Board
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Post
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

    public function setLastThread(Thread $lastThread)
    {
        $this->lastThread = $lastThread;

        return $this;
    }

    public function getLastThread()
    {
        return $this->lastThread;
    }

    public function setPostNumber($postNumber)
    {
        $this->postNumber = $postNumber;

        return $this;
    }

    public function getPostNumber()
    {
        return $this->postNumber;
    }

    public function incPostNumber()
    {
        ++$this->postNumber;
    }

    public function setThreadNumber($threadNumber)
    {
        $this->threadNumber = $threadNumber;

        return $this;
    }

    public function getThreadNumber()
    {
        return $this->threadNumber;
    }

    public function incThreadNumber()
    {
        ++$this->threadNumber;
    }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }
}
