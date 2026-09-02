<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ein Abonnement auf einer der vier Ebenen des Forums: ein einzelnes Thema, ein Forum,
 * das Forum einer Stadt oder alles.
 *
 * Die vier Felder schliessen einander aus — genau eines ist gesetzt beziehungsweise
 * globalScope ist wahr. Eine Datenbank-Bedingung kann das nicht erzwingen, weil MySQL
 * NULL-Werte in einem eindeutigen Index nicht als gleich behandelt; die Eindeutigkeit
 * sichert deshalb ForumSubscriptionRepository::findExisting().
 */
#[ORM\Table(name: 'forum_subscription')]
#[ORM\Index(fields: ['user'], name: 'forum_subscription_user_index')]
#[ORM\Entity(repositoryClass: 'App\Repository\ForumSubscriptionRepository')]
class ForumSubscription
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    protected ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Thread::class)]
    #[ORM\JoinColumn(name: 'thread_id', referencedColumnName: 'id', nullable: true)]
    protected ?Thread $thread = null;

    #[ORM\ManyToOne(targetEntity: Board::class)]
    #[ORM\JoinColumn(name: 'board_id', referencedColumnName: 'id', nullable: true)]
    protected ?Board $board = null;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id', nullable: true)]
    protected ?City $city = null;

    #[ORM\Column(name: 'global_scope', type: 'boolean', options: ['default' => false])]
    protected bool $globalScope = false;

    #[ORM\Column(type: 'datetime')]
    protected \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): ForumSubscription
    {
        $this->user = $user;

        return $this;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): ForumSubscription
    {
        $this->thread = $thread;

        return $this;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): ForumSubscription
    {
        $this->board = $board;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): ForumSubscription
    {
        $this->city = $city;

        return $this;
    }

    public function isGlobalScope(): bool
    {
        return $this->globalScope;
    }

    public function setGlobalScope(bool $globalScope): ForumSubscription
    {
        $this->globalScope = $globalScope;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): ForumSubscription
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Woran hängt dieses Abonnement? Für die Anzeige in der Kontoverwaltung.
     */
    public function getLabel(): string
    {
        if ($this->globalScope) {
            return 'Das gesamte Forum';
        }

        return $this->thread?->getTitle()
            ?? $this->board?->getTitle()
            ?? $this->city?->getTitle()
            ?? 'Unbekannt';
    }
}
