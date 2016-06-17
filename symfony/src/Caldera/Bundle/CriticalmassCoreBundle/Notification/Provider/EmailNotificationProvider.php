<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider;

use Caldera\Bundle\CalderaBundle\Entity\User;

class EmailNotificationProvider extends AbstractNotificationProvider
{
    protected $mailer;
    protected $senderAddress;

    public function __construct($mailer, $senderAddress)
    {
        $this->mailer = $mailer;
        $this->senderAddress = $senderAddress;
    }

    public function send()
    {
        /** @var User $user */
        foreach ($this->userList as $user) {
            echo $user->getEmail();
            $message = \Swift_Message::newInstance()
                ->setSubject($this->notification->getTitle())
                ->setFrom($this->senderAddress)
                ->setTo($user->getEmail())
                ->setBody(
                    $this->notification->getMessage(),
                    'text/plain'
                );

            $this->mailer->send($message);
        }

    }
}