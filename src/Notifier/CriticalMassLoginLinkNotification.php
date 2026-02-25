<?php declare(strict_types=1);

namespace App\Notifier;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class CriticalMassLoginLinkNotification extends LoginLinkNotification
{
    public function __construct(private LoginLinkDetails $loginLinkDetails, string $subject, array $channels = [])
    {
        parent::__construct($loginLinkDetails, $subject, $channels);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new TemplatedEmail())
            ->from(new Address('malte@criticalmass.in', 'criticalmass.in'))
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->htmlTemplate('email/login_link.html.twig')
            ->context([
                'loginUrl' => $this->loginLinkDetails->getUrl(),
                'expirationText' => $this->getExpirationText(),
                'content' => $this->getContent() ?: $this->getDefaultContent(),
            ]);

        return new EmailMessage($email);
    }

    private function getExpirationText(): string
    {
        $duration = $this->loginLinkDetails->getExpiresAt()->getTimestamp() - time();
        $minutes = (int) floor($duration / 60);
        $hours = (int) floor($duration / 3600);

        if ($hours >= 1) {
            return $hours === 1 ? '1 Stunde' : sprintf('%d Stunden', $hours);
        }

        return sprintf('%d Minuten', $minutes);
    }

    private function getDefaultContent(): string
    {
        return 'Klick auf den Button und du bist dabei — deine Critical Mass wartet schon auf dich!';
    }
}
