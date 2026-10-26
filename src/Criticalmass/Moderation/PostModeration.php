<?php declare(strict_types=1);

namespace App\Criticalmass\Moderation;

use App\Criticalmass\Forum\ContentWithdrawal;
use App\Entity\Post;
use App\Entity\PostReport;
use App\Entity\User;
use App\Enum\PostReportStatusEnum;
use App\Repository\PostReportRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Entscheidet ueber gemeldete Beitraege.
 *
 * Entschieden wird pro Beitrag, nicht pro Meldung: Haben drei Leute denselben
 * Beitrag gemeldet, schliesst eine Entscheidung alle drei Meldungen.
 */
class PostModeration
{
    public function __construct(
        private readonly PostReportRepository $reportRepository,
        private readonly ContentWithdrawal $contentWithdrawal,
        private readonly ModerationMailer $mailer,
        private readonly ManagerRegistry $registry
    ) {
    }

    /**
     * Nimmt den Beitrag aus der Anzeige. Ist er der erste Beitrag eines Themas,
     * geht das ganze Thema mit — ein Thema ohne Anfang ergibt keinen Sinn, und
     * genauso handhabt es das Forum beim Zurueckziehen.
     *
     * @return int Zahl der geschlossenen Meldungen
     */
    public function remove(Post $post, User $moderator, string $reason): int
    {
        // Entfernt wird nur auf eine Meldung hin; dafuer gibt es sonst das Zurueckziehen.
        if ([] === $this->reportRepository->findOpenForPost($post)) {
            return 0;
        }

        $thread = $post->getThread();

        // Das Thema nimmt seine Beitraege mit; den ersten Beitrag zusaetzlich
        // einzeln zurueckzuziehen, wuerde die Zaehler doppelt senken.
        if (null !== $thread && $thread->getFirstPost() === $post) {
            $this->contentWithdrawal->withdrawThread($thread);
        } else {
            $this->contentWithdrawal->withdrawPost($post);
        }

        $reports = $this->decide($post, PostReportStatusEnum::REMOVED, $moderator, $reason);

        if ([] !== $reports) {
            $this->mailer->postRemoved($post, $reports[0]);
        }

        return count($reports);
    }

    /**
     * Laesst den Beitrag stehen.
     *
     * @return int Zahl der geschlossenen Meldungen
     */
    public function dismiss(Post $post, User $moderator, ?string $note = null): int
    {
        return count($this->decide($post, PostReportStatusEnum::DISMISSED, $moderator, $note));
    }

    /**
     * @return list<PostReport>
     */
    private function decide(Post $post, PostReportStatusEnum $status, User $moderator, ?string $note): array
    {
        $reports = $this->reportRepository->findOpenForPost($post);

        foreach ($reports as $report) {
            $report->decide($status, $moderator, $note);
        }

        $this->registry->getManager()->flush();

        foreach ($reports as $report) {
            $this->mailer->reportDecided($report);
        }

        return $reports;
    }
}
