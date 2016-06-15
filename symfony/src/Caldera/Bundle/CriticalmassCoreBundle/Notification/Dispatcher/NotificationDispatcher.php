<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Notification\Dispatcher;

use Caldera\Bundle\CalderaBundle\Entity\NotificationSubscription;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Notification;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider\PushoverNotificationProvider;
use Doctrine\ORM\EntityManager;

class NotificationDispatcher
{
    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    protected $pushoverProvider;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();

        $this->pushoverProvider = new PushoverNotificationProvider();
    }

    public function setNotification(Notification $notification)
    {
        $this->pushoverProvider->setNotification($notification);
    }

    public function dispatch()
    {
        $subscriptions = $this->entityManager->getRepository('CalderaBundle:NotificationSubscription')->findAll();

        /** @var NotificationSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            if ($subscription->getNotifyByPushover()) {
                $this->pushoverProvider->addUser($subscription->getUser());
            }
        }
        
    }

    public function send()
    {
        $this->pushoverProvider->send();
    }
}