<?php declare(strict_types=1);

namespace App\Criticalmass\Mailer;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailer;

class MailHandler implements MailerInterface
{
    public function __construct(
        protected SymfonyMailer $mailer,
        protected UrlGeneratorInterface $router,
        protected Environment $twig
    ) {

    }

    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        $url = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $text = sprintf('Hej %s,', $user->getUsername())."\n\n";
        $text .= 'Danke, dass du dir ein Benutzerkonto auf criticalmass.in registriert hast.'."\n";
        $text .= sprintf('Bitte klicke auf den folgenden Link, um einmalig dein Benutzerkonto zu bestätigen: %s', $url)."\n\n";
        $text .= 'Viele Grüße aus Lüneburg'."\n";
        $text .= 'Malte'."\n";

        $email = (new Email())
            ->from('maltehuebner@gmx.org')
            ->to($user->getEmail())
            ->subject('Setze jetzt dein Kennwort für criticalmass.in zurück')
            ->text($text)
        ;

        $this->mailer->send($email);
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $text = sprintf('Hej %s,', $user->getUsername())."\n\n";
        $text .= sprintf('Bitte klicke auf den folgenden Link, um dein Kennwort auf criticalmass.in zurückzusetzen: %s', $url)."\n\n";
        $text .= 'Viele Grüße aus Lüneburg'."\n";
        $text .= 'Malte'."\n";

        $email = (new Email())
            ->from('maltehuebner@gmx.org')
            ->to($user->getEmail())
            ->subject('Setze jetzt dein Kennwort für criticalmass.in zurück')
            ->text($text)
        ;

        $this->mailer->send($email);
    }
}