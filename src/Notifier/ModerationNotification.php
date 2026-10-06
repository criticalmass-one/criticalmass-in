<?php declare(strict_types=1);

namespace App\Notifier;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

/**
 * Eine Mail rund um Meldungen und Moderation. Die Texte setzt der ModerationMailer.
 */
class ModerationNotification extends Notification implements EmailNotificationInterface
{
    /**
     * @param list<string> $paragraphs
     */
    public function __construct(
        string $subject,
        private readonly string $heading,
        private readonly array $paragraphs,
        private readonly ?string $quote,
        private readonly ?string $buttonLabel,
        private readonly ?string $buttonUrl,
        private readonly string $senderAddress
    ) {
        parent::__construct($subject, ['email']);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderAddress, 'criticalmass.in'))
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->htmlTemplate('email/moderation.html.twig')
            ->context([
                'heading' => $this->heading,
                'paragraphs' => $this->paragraphs,
                'quote' => $this->quote,
                'buttonLabel' => $this->buttonLabel,
                'buttonUrl' => $this->buttonUrl,
            ]);

        return new EmailMessage($email);
    }
}
