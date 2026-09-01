<?php declare(strict_types=1);

namespace App\Notifier;

use App\Entity\Post;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class ForumPostNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private readonly Post $post,
        private readonly string $threadTitle,
        private readonly string $postUrl,
        private readonly string $settingsUrl,
        private readonly string $senderAddress = 'noreply@criticalmass.in'
    ) {
        parent::__construct(sprintf('Neuer Beitrag: %s', $threadTitle), ['email']);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderAddress, 'criticalmass.in'))
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->htmlTemplate('email/forum_post.html.twig')
            ->context([
                'threadTitle' => $this->threadTitle,
                'authorName' => $this->post->getUser()?->getUsername() ?? 'Jemand',
                'excerpt' => $this->excerpt(),
                'postUrl' => $this->postUrl,
                'settingsUrl' => $this->settingsUrl,
            ]);

        return new EmailMessage($email);
    }

    private function excerpt(): string
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $this->post->getMessage()) ?? '');

        return mb_strlen($text) > 300 ? mb_substr($text, 0, 300) . ' …' : $text;
    }
}
