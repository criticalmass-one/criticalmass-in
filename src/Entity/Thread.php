<?php declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Criticalmass\Router\Attribute as Routing;

#[ORM\Table(name: 'thread')]
#[ORM\Entity(repositoryClass: 'App\Repository\ThreadRepository')]
class Thread implements RouteableInterface, PostableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[Routing\RouteParameter(name: 'boardSlug')]
    #[ORM\ManyToOne(targetEntity: 'Board', inversedBy: 'threads')]
    #[ORM\JoinColumn(name: 'board_id', referencedColumnName: 'id')]
    protected ?Board $board = null;

    #[Routing\RouteParameter(name: 'citySlug')]
    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'threads')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    protected ?City $city = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $title = null;

    #[Routing\RouteParameter(name: 'threadSlug')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $slug = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected int $postNumber = 0;

    #[ORM\OneToOne(targetEntity: 'Post')]
    #[ORM\JoinColumn(name: 'firstpost_id', referencedColumnName: 'id', unique: true)]
    protected ?Post $firstPost = null;

    #[ORM\OneToOne(targetEntity: 'Post')]
    #[ORM\JoinColumn(name: 'lastpost_id', referencedColumnName: 'id', unique: true)]
    protected ?Post $lastPost = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $enabled = true;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(?string $title = null): Thread
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setEnabled(bool $enabled): Thread
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setCity(?City $city = null): Thread
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setBoard(?Board $board = null): Thread
    {
        $this->board = $board;

        return $this;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setFirstPost(?Post $firstPost = null): Thread
    {
        $this->firstPost = $firstPost;

        return $this;
    }

    public function getFirstPost(): ?Post
    {
        return $this->firstPost;
    }

    public function setLastPost(?Post $lastPost = null): Thread
    {
        $this->lastPost = $lastPost;

        return $this;
    }

    public function getLastPost(): ?Post
    {
        return $this->lastPost;
    }

    public function setPostNumber(int $postNumber): Thread
    {
        $this->postNumber = $postNumber;

        return $this;
    }

    public function getPostNumber(): int
    {
        return $this->postNumber;
    }

    public function incPostNumber(): Thread
    {
        ++$this->postNumber;

        return $this;
    }

    public function setSlug(string $slug): Thread
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
