<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Entity;

use Caldera\Bundle\CriticalmassModelBundle\EntityInterface\ViewableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="thread")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CriticalmassModelBundle\Repository\ThreadRepository")
 */
class Thread implements ViewableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="threads")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="threads")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id")
     */
    protected $board;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $postNumber = 0;
    /**
     * @ORM\OneToOne(targetEntity="Post")
     * @ORM\JoinColumn(name="firstpost_id", referencedColumnName="id")
     */
    protected $firstPost;

    /**
     * @ORM\OneToOne(targetEntity="Post")
     * @ORM\JoinColumn(name="lastpost_id", referencedColumnName="id")
     */
    protected $lastPost;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return Post
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
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

    /**
     * Set city
     *
     * @param City $city
     * @return Thread
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return Ride
     */
    public function getCity()
    {
        return $this->city;
    }

    public function setBoard(Board $board = null)
    {
        $this->board = $board;

        return $this;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function setFirstPost(Post $firstPost)
    {
        $this->firstPost = $firstPost;

        return $this;
    }

    public function getFirstPost()
    {
        return $this->firstPost;
    }

    public function setLastPost(Post $lastPost)
    {
        $this->lastPost = $lastPost;

        return $this;
    }

    public function getLastPost()
    {
        return $this->lastPost;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function incViews()
    {
        ++$this->views;
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
