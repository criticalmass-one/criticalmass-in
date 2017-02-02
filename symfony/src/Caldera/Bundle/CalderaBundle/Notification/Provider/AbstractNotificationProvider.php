<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider;

use Caldera\Bundle\CalderaBundle\Entity\User;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Notification\AbstractNotification;

abstract class AbstractNotificationProvider
{
    /** @var AbstractNotification $notification */
    protected $notification;

    protected $userList = [];

    public function setNotification(AbstractNotification $notification)
    {
        $this->notification = $notification;
    }

    public function addUser(User $user)
    {
        array_push($this->userList, $user);
    }

    abstract public function send();

    protected function createArchiveNotification()
    {
    }
}