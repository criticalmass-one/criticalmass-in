<?php declare(strict_types=1);

namespace App\Entity;

use App\Enum\PostReportReasonEnum;
use App\Enum\PostReportStatusEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Eine Meldung zu einem Beitrag, samt der Entscheidung darueber.
 *
 * Gemeldet wird von angemeldeten Nutzern (`reporter`) oder von Gaesten, die
 * dann eine Adresse hinterlassen (`reporterEmail`). Beide erfahren, wie
 * entschieden wurde (Art. 16 Abs. 5 DSA). Wird der Beitrag entfernt, geht die
 * Begruendung in `decisionNote` auch an den Autor (Art. 17 DSA).
 */
#[ORM\Table(name: 'post_report')]
#[ORM\Entity(repositoryClass: 'App\Repository\PostReportRepository')]
#[ORM\Index(fields: ['status'], name: 'post_report_status_index')]
class PostReport
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'reporter_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $reporter = null;

    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $reporterEmail = null;

    #[Assert\NotNull(message: 'Bitte wähle einen Grund.')]
    #[ORM\Column(type: 'string', length: 16, enumType: PostReportReasonEnum::class)]
    private ?PostReportReasonEnum $reason = null;

    #[Assert\Length(max: 2000)]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $explanation = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'string', length: 16, enumType: PostReportStatusEnum::class, options: ['default' => 'OPEN'])]
    private PostReportStatusEnum $status = PostReportStatusEnum::OPEN;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $decidedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'decided_by_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $decidedBy = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $decisionNote = null;

    public function __construct(Post $post)
    {
        $this->post = $post;
        $this->createdAt = new \DateTime();
    }

    #[Assert\Callback]
    public function validateExplanation(ExecutionContextInterface $context): void
    {
        if ($this->reason?->requiresExplanation() && '' === trim((string) $this->explanation)) {
            $context->buildViolation('Bitte erkläre kurz, was an diesem Beitrag nicht stimmt.')
                ->atPath('explanation')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): self
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getReporterEmail(): ?string
    {
        return $this->reporterEmail;
    }

    public function setReporterEmail(?string $reporterEmail): self
    {
        $this->reporterEmail = $reporterEmail;

        return $this;
    }

    /**
     * Wohin die Entscheidung geht: an das Konto, sonst an die angegebene Adresse.
     */
    public function getContactEmail(): ?string
    {
        return $this->reporter?->getEmail() ?? $this->reporterEmail;
    }

    public function getReason(): ?PostReportReasonEnum
    {
        return $this->reason;
    }

    public function setReason(?PostReportReasonEnum $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): self
    {
        $this->explanation = $explanation;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getStatus(): PostReportStatusEnum
    {
        return $this->status;
    }

    public function isOpen(): bool
    {
        return PostReportStatusEnum::OPEN === $this->status;
    }

    public function decide(PostReportStatusEnum $status, User $moderator, ?string $note): self
    {
        $this->status = $status;
        $this->decidedBy = $moderator;
        $this->decidedAt = new \DateTime();
        $this->decisionNote = $note;

        return $this;
    }

    public function getDecidedAt(): ?\DateTime
    {
        return $this->decidedAt;
    }

    public function getDecidedBy(): ?User
    {
        return $this->decidedBy;
    }

    public function getDecisionNote(): ?string
    {
        return $this->decisionNote;
    }
}
