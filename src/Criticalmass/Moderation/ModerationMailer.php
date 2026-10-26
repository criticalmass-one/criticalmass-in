<?php declare(strict_types=1);

namespace App\Criticalmass\Moderation;

use App\Entity\Post;
use App\Entity\PostReport;
use App\Enum\PostReportStatusEnum;
use App\Notifier\ModerationNotification;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Die Mails rund um eine Meldung.
 *
 * - Die Admins erfahren von jeder neuen Meldung.
 * - Wer meldet, bekommt eine Eingangsbestaetigung (Art. 16 Abs. 4 DSA) und
 *   spaeter die Entscheidung (Art. 16 Abs. 5 DSA).
 * - Wessen Beitrag entfernt wird, bekommt eine Begruendung (Art. 17 DSA).
 *
 * Eine unzustellbare Mail bricht nichts ab; sie wird protokolliert.
 */
class ModerationMailer
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly UserRepository $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LoggerInterface $logger,
        #[Autowire('%notification.mail.sender_address%')] private readonly string $senderAddress
    ) {
    }

    public function reportReceived(PostReport $report): void
    {
        $reason = (string) $report->getReason()?->label();

        $paragraphs = [sprintf('Grund: %s', $reason)];

        if ($report->getExplanation()) {
            $paragraphs[] = sprintf('Erklärung: %s', $report->getExplanation());
        }

        $paragraphs[] = sprintf('Der gemeldete Beitrag von %s:', $this->authorName($report->getPost()));

        $toAdmins = $this->notification(
            sprintf('Neue Meldung: %s', $reason),
            'Ein Beitrag wurde gemeldet',
            $paragraphs,
            $this->excerpt($report->getPost()),
            'Zur Moderation',
            $this->urlGenerator->generate('caldera_criticalmass_moderation_reports', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        foreach ($this->userRepository->findAdmins() as $admin) {
            $this->send($toAdmins, $admin->getEmail());
        }

        $this->send($this->notification(
            'Deine Meldung ist bei uns eingegangen',
            'Danke für deine Meldung',
            [
                sprintf('Wir haben deine Meldung zu einem Beitrag auf criticalmass.in erhalten (Grund: %s).', $reason),
                'Ein Mensch aus unserem Team schaut sie sich an. Sobald wir entschieden haben, bekommst du eine weitere Mail.',
            ],
            $this->excerpt($report->getPost()),
            null,
            null
        ), $report->getContactEmail());
    }

    public function reportDecided(PostReport $report): void
    {
        $removed = PostReportStatusEnum::REMOVED === $report->getStatus();

        $this->send($this->notification(
            'Wir haben über deine Meldung entschieden',
            $removed ? 'Der Beitrag wurde entfernt' : 'Der Beitrag bleibt stehen',
            [
                sprintf('Du hattest am %s einen Beitrag auf criticalmass.in gemeldet.', $report->getCreatedAt()->format('d.m.Y')),
                $removed
                    ? 'Wir haben den Beitrag daraufhin entfernt. Danke, dass du uns darauf aufmerksam gemacht hast.'
                    : 'Wir haben uns den Beitrag angesehen und keinen Grund gefunden, ihn zu entfernen. Er bleibt deshalb sichtbar.',
                'Wenn du die Entscheidung für falsch hältst, erreichst du uns über die Kontaktangaben im Impressum.',
            ],
            $this->excerpt($report->getPost()),
            null,
            null
        ), $report->getContactEmail());
    }

    public function postRemoved(Post $post, PostReport $report): void
    {
        $this->send($this->notification(
            'Dein Beitrag auf criticalmass.in wurde entfernt',
            'Dein Beitrag wurde entfernt',
            [
                sprintf('Dein Beitrag vom %s ist nicht mehr sichtbar. Er wurde uns gemeldet (Grund: %s), und ein Mensch aus unserem Team hat ihn geprüft; die Entscheidung ist nicht automatisiert gefallen.', $post->getDateTime()->format('d.m.Y'), (string) $report->getReason()?->label()),
                sprintf('Begründung: %s', (string) $report->getDecisionNote()),
                'Wenn du die Entscheidung für falsch hältst, kannst du widersprechen: Schreib uns über die Kontaktangaben im Impressum. Außerdem steht dir der Rechtsweg offen.',
            ],
            $this->excerpt($post),
            'Impressum',
            $this->urlGenerator->generate('caldera_criticalmass_static_display', ['slug' => 'impress'], UrlGeneratorInterface::ABSOLUTE_URL)
        ), $post->getUser()?->getEmail());
    }

    /**
     * @param list<string> $paragraphs
     */
    private function notification(string $subject, string $heading, array $paragraphs, ?string $quote, ?string $buttonLabel, ?string $buttonUrl): ModerationNotification
    {
        return new ModerationNotification($subject, $heading, $paragraphs, $quote, $buttonLabel, $buttonUrl, $this->senderAddress);
    }

    private function send(ModerationNotification $notification, ?string $email): void
    {
        if (null === $email || '' === $email) {
            return;
        }

        try {
            $this->notifier->send($notification, new Recipient($email));
        } catch (\Throwable $exception) {
            $this->logger->error('Moderations-Mail konnte nicht zugestellt werden', [
                'subject' => $notification->getSubject(),
                'exception' => $exception,
            ]);
        }
    }

    private function authorName(Post $post): string
    {
        return $post->getUser()?->getUsername() ?? 'jemandem';
    }

    private function excerpt(Post $post): string
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $post->getText()) ?? '');

        return mb_strlen($text) > 300 ? mb_substr($text, 0, 300) . ' …' : $text;
    }
}
