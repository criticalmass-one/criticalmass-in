<?php declare(strict_types=1);

namespace App\Notifier;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
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
        if (!class_exists(NotificationEmail::class)) {
            throw new \LogicException(sprintf('The "%s" method requires "symfony/twig-bridge:>4.4".', __METHOD__));
        }

        $email = NotificationEmail::asPublicEmail()
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->content($this->getContent() ?: $this->getDefaultContent())
            ->action('Login', $this->loginLinkDetails->getUrl())
        ;

        return new EmailMessage($email);
    }

    private function getDefaultContent(): string
    {
        $duration = $this->loginLinkDetails->getExpiresAt()->getTimestamp() - time();
        $durationString = floor($duration / 60).' minute'.($duration > 60 ? 's' : '');
        if (($hours = $duration / 3600) >= 1) {
            $durationString = floor($hours).' hour'.($hours >= 2 ? 's' : '');
        }

        return sprintf(
        'Bitte click auf den Link, um dich auf criticalmass.in einzuloggen.'
             . ' Dieser Link läuft in %s ab.',
            $durationString
        );
    }
}
