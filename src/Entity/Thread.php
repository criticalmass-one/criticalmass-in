<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\PostableInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Criticalmass\Router\Annotation as Routing;

/**
 * @ORM\Table(name="thread")
 * @ORM\Entity(repositoryClass="App\Repository\ThreadRepository")

 */
class Thread implements ViewableEntity, RouteableInterface, AutoParamConverterAble, PostableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="threads")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id")
     * @Routing\RouteParameter(name="boardSlug")
     */
    protected $board;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="threads")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @Routing\RouteParameter(name="citySlug")
     */
    protected $city;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Routing\RouteParameter(name="threadSlug")
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
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(string $title = null): Thread
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

    public function setCity(City $city = null): Thread
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setBoard(Board $board = null): Thread
    {
        $this->board = $board;

        return $this;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setFirstPost(Post $firstPost = null): Thread
    {
        $this->firstPost = $firstPost;

        return $this;
    }

    public function getFirstPost(): ?Post
    {
        return $this->firstPost;
    }

    public function setLastPost(Post $lastPost = null): Thread
    {
        $this->lastPost = $lastPost;

        return $this;
    }

    public function getLastPost(): ?Post
    {
        return $this->lastPost;
    }

    public function setViews(int $views): ViewableEntity
    {
        $this->views = $views;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function incViews(): ViewableEntity
    {
        ++$this->views;

        return $this;
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
