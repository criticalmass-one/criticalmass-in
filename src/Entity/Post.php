<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MalteHuebner\DataQueryBundle\Attribute\EntityAttribute as DataQuery;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'post')]
#[ORM\Entity(repositoryClass: 'App\Repository\PostRepository')]
#[ORM\Index(fields: ['dateTime'], name: 'post_date_time_index')]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[DataQuery\Sortable]
    #[Groups(['post-list'])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'Post', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?Post $parent = null;

    #[ORM\OneToMany(targetEntity: 'Post', mappedBy: 'parent')]
    #[Ignore]
    protected Collection $children;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[Groups(['post-list'])]
    protected ?User $user = null;

    #[DataQuery\Queryable]
    #[ORM\ManyToOne(targetEntity: 'Ride', inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'ride_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?Ride $ride = null;

    #[DataQuery\Queryable]
    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?City $city = null;

    #[ORM\ManyToOne(targetEntity: 'Thread', inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'thread_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?Thread $thread = null;

    #[ORM\ManyToOne(targetEntity: 'Photo', inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'photo_id', referencedColumnName: 'id')]
    #[Ignore]
    protected ?Photo $photo = null;

    #[DataQuery\Queryable]
    #[DataQuery\Sortable]
    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['post-list'])]
    protected ?float $latitude = null;

    #[DataQuery\Queryable]
    #[DataQuery\Sortable]
    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['post-list'])]
    protected ?float $longitude = null;

    #[DataQuery\DateTimeQueryable(format: 'strict_date_hour_minute_second', pattern: 'Y-m-d\TH:i:s')]
    #[DataQuery\Sortable]
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['post-list'])]
    protected ?\DateTime $dateTime = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['post-list'])]
    protected ?string $message = null;

    #[DataQuery\DefaultBooleanValue(alias: 'isEnabled', value: true)]
    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Ignore]
    protected bool $enabled = true;

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

    public function setUser(?User $user = null): Post
    {
        $this->user = $user;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(?Ride $ride = null): Post
    {
        $this->ride = $ride;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city = null): Post
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

    public function setParent(?Post $parent = null): Post
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

    public function setThread(?Thread $thread = null): Post
    {
        $this->thread = $thread;

        return $this;
    }

    #[Groups(['post-list'])]
    public function getRideId(): ?int
    {
        return $this->ride?->getId();
    }

    #[Groups(['post-list'])]
    public function getCityId(): ?int
    {
        return $this->city?->getId();
    }

    #[Groups(['post-list'])]
    public function getCitySlug(): ?string
    {
        return $this->city?->getMainSlugString();
    }

    #[Groups(['post-list'])]
    public function getPhotoId(): ?int
    {
        return $this->photo?->getId();
    }

    #[Groups(['post-list'])]
    public function getThreadId(): ?int
    {
        return $this->thread?->getId();
    }

    /** TODO remove this and rename $message to $text */
    public function getText(): string
    {
        return $this->message;
    }
}
