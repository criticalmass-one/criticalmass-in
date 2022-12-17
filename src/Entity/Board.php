<?php declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\BoardInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Criticalmass\Router\Annotation as Routing;

/**
 * @Routing\DefaultRoute(name="caldera_criticalmass_board_listthreads")
 */
#[ORM\Table(name: 'board')]
#[ORM\Entity(repositoryClass: 'App\Repository\BoardRepository')]
class Board implements BoardInterface, RouteableInterface, AutoParamConverterAble
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected int $threadNumber = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected int $postNumber = 0;

    #[ORM\OneToOne(targetEntity: 'Thread')]
    #[ORM\JoinColumn(name: 'lastthread_id', referencedColumnName: 'id', unique: true)]
    protected ?Thread $lastThread = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected int $position = 0;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected bool $enabled = true;

    /**
     * @Routing\RouteParameter(name="boardSlug")
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $slug = null;

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
