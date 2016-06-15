<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider;


use Caldera\Bundle\CalderaBundle\Entity\User;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Notification;

class PushoverNotificationProvider
{
    /** @var Notification $notification */
    protected $notification;

    protected $userList = [];

    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function addUser(User $user)
    {
        array_push($this->userList, $user);
    }

    public function send()
    {
        $ch = curl_init();

        /** @var User $user */
        foreach ($this->userList as $user) {
            $options = [
                CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                CURLOPT_POSTFIELDS => [
                    "token" => "wP7MBPTf5TFvazDCtWf2mL1eH9m1fK",
                    "user" => $user->getPushoverToken(),
                    "message" => $this->notification->getShortMessage()
                ],
                CURLOPT_SAFE_UPLOAD => true,
            ];

            curl_setopt_array($ch, $options);
            curl_exec($ch);
        }

        curl_close($ch);
    }
}