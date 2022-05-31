<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Website\Crawler\Crawlable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post implements Crawlable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected ?Post $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="parent")
     */
    protected Collection $children;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Ride", inversedBy="posts")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected ?Ride $ride = null;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City", inversedBy="posts")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected ?City $city = null;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Thread")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    protected ?Thread $thread = null;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id")
     */
    protected ?Photo $photo = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected ?float $latitude = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected ?float $longitude = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTime $dateTime = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected ?string $message = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected bool $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $crawled = false;

    public function __construct()
    {
        $this->dateTime = new \DateTime();

        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): Post
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): Post
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): Post
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): Post
    {
        $this->message = $message;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): Post
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): Post
    {
        $this->user = $user;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null): Post
    {
        $this->ride = $ride;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): Post
    {
        $this->city = $city;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(Photo $photo): Post
    {
        $this->photo = $photo;

        return $this;
    }

    public function getParent(): ?Post
    {
        return $this->parent;
    }

    public function setParent(Post $parent = null): Post
    {
        $this->parent = $parent;

        return $this;
    }

    public function addChild(Post $child): Post
    {
        $this->children->add($child);

        return $this;
    }

    public function removeChild(Post $child): Post
    {
        $this->children->removeElement($child);

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread = null): Post
    {
        $this->thread = $thread;

        return $this;
    }

    /** TODO remove this and rename $message to $text */
    public function getText(): string
    {
        return $this->message;
    }

    public function isCrawled(): bool
    {
        return $this->crawled;
    }

    public function setCrawled(bool $crawled): Crawlable
    {
        $this->crawled = $crawled;

        return $this;
    }
}
