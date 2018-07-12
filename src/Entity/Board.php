<?php declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\BoardInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Criticalmass\Router\Annotation as Routing;

/**
 * @ORM\Table(name="board")
 * @ORM\Entity(repositoryClass="App\Repository\BoardRepository")
 * @Routing\DefaultRoute(name="caldera_criticalmass_board_listthreads")
 */
class Board implements BoardInterface, RouteableInterface, AutoParamConverterAble
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
     * @Routing\RouteParameter(name="boardSlug")
     */
    protected $slug;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(string $title): BoardInterface
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): Board
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setEnabled(bool $enabled): Board
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setLastThread(Thread $lastThread): BoardInterface
    {
        $this->lastThread = $lastThread;

        return $this;
    }

    public function getLastThread(): ?Thread
    {
        return $this->lastThread;
    }

    public function setPostNumber(int $postNumber): BoardInterface
    {
        $this->postNumber = $postNumber;

        return $this;
    }

    public function getPostNumber(): int
    {
        return $this->postNumber;
    }

    public function incPostNumber(): BoardInterface
    {
        ++$this->postNumber;

        return $this;
    }

    public function setThreadNumber(int $threadNumber): BoardInterface
    {
        $this->threadNumber = $threadNumber;

        return $this;
    }

    public function getThreadNumber(): int
    {
        return $this->threadNumber;
    }

    public function incThreadNumber(): BoardInterface
    {
        ++$this->threadNumber;

        return $this;
    }

    public function setPosition(int $position): Board
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setSlug(string $slug): Board
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
