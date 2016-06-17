<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider;

use Caldera\Bundle\CalderaBundle\Entity\User;

class PushoverNotificationProvider extends AbstractNotificationProvider
{
    protected $pushoverToken;

    public function __construct($pushoverToken)
    {
        $this->pushoverToken = $pushoverToken;
    }

    public function send()
    {
        $ch = curl_init();

        /** @var User $user */
        foreach ($this->userList as $user) {
            if (!$user->getPushoverToken()) {
                continue;
            }

            $options = [
                CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                CURLOPT_POSTFIELDS => [
                    "token" => $this->pushoverToken,
                    "user" => $user->getPushoverToken(),
                    "message" => $this->notification->getShortMessage()
                ],
                CURLOPT_SAFE_UPLOAD => true,
                CURLOPT_RETURNTRANSFER => true
            ];

            curl_exec($ch);
        }

        curl_close($ch);
    }
}