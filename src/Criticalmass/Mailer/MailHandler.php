<?php declare(strict_types=1);

namespace App\Criticalmass\Mailer;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

class MailHandler implements MailerInterface
{

    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        dd($user);
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        dd($user);
    }
}