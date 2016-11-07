<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="posts")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="posts")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Thread", inversedBy="posts")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="posts")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     */
    protected $photo;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="posts")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="posts")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    protected $event;

    /**
     * @ORM\ManyToOne(targetEntity="BlogPost", inversedBy="posts")
     * @ORM\JoinColumn(name="blog_post_id", referencedColumnName="id")
     */
    protected $blogPost;

    /**
     * @ORM\ManyToOne(targetEntity="Incident", inversedBy="posts")
     * @ORM\JoinColumn(name="incident_id", referencedColumnName="id")
     */
    protected $inicident;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

    /**
     * @ORM\ManyToOne(targetEntity="AnonymousName", inversedBy="posts")
     * @ORM\JoinColumn(name="anonymous_name_id", referencedColumnName="id")
     */
    protected $anonymousName;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $message;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $chat = false;

    /**
     * @ORM\Column(type="integer")
     */
    protected $colorRed = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $colorGreen = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $colorBlue = 0;


    /**
     * @ORM\Column(type="boolean")
     */
    protected $obfuscated = 0;

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
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Post
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Post
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

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
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Post
     */
    public function setMessage($message)
    {
        $this->message = $message;

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
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Post
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get ride
     *
     * @return Ride
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Set ride
     *
     * @param Ride $ride
     * @return Post
     */
    public function setRide(Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city
     *
     * @param City $city
     * @return Post
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    public function getBlogPost()
    {
        return $this->blogPost;
    }

    public function setBlogPost(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * Get parent
     *
     * @return Post
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param Post $parent
     * @return Post
     */
    public function setParent(Post $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Add children
     *
     * @param Post $children
     * @return Post
     */
    public function addChild(Post $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Post $children
     */
    public function removeChild(Post $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get content
     *
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param Content $content
     * @return Post
     */
    public function setContent(Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get thread
     *
     * @return Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set thread
     *
     * @param Thread $thread
     * @return Post
     */
    public function setThread(Thread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set event
     *
     * @param Event $event
     * @return Post
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    public function getIncident()
    {
        return $this->incident;
    }

    public function setIncident(Incident $incident): Post
    {
        $this->incident = $incident;

        return $this;
    }

    /**
     * Get chat
     *
     * @return boolean
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * Set chat
     *
     * @param boolean $chat
     * @return Post
     */
    public function setChat($chat)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get anonymousName
     *
     * @return AnonymousName
     */
    public function getAnonymousName()
    {
        return $this->anonymousName;
    }

    /**
     * Set anonymousName
     *
     * @param AnonymousName $anonymousName
     * @return Post
     */
    public function setAnonymousName($anonymousName)
    {
        $this->anonymousName = $anonymousName;

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
     * Set colorRed
     *
     * @param integer $colorRed
     * @return City
     */
    public function setColorRed($colorRed)
    {
        $this->colorRed = $colorRed;

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
     * Set colorGreen
     *
     * @param integer $colorGreen
     * @return City
     */
    public function setColorGreen($colorGreen)
    {
        $this->colorGreen = $colorGreen;

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
     * Set colorBlue
     *
     * @param integer $colorBlue
     * @return City
     */
    public function setColorBlue($colorBlue)
    {
        $this->colorBlue = $colorBlue;

        return $this;
    }

    /**
     * Get obfuscated
     *
     * @return boolean
     */
    public function getObfuscated()
    {
        return $this->obfuscated;
    }

    /**
     * Set obfuscated
     *
     * @param boolean $obfuscated
     * @return Post
     */
    public function setObfuscated($obfuscated)
    {
        $this->obfuscated = $obfuscated;

        return $this;
    }
}
