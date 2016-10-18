<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Notification\Dispatcher;

use Caldera\Bundle\CalderaBundle\Entity\NotificationSubscription;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Notification\AbstractNotification;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider\EmailNotificationProvider;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider\PushoverNotificationProvider;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider\ShortmessageNotificationProvider;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class NotificationDispatcher
{
    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /** @var PushoverNotificationProvider $pushoverProvider */
    protected $pushoverProvider;

    /** @var EmailNotificationProvider $emailProvider */
    protected $emailProvider;

    /** @var ShortmessageNotificationProvider $shortmessageProvider */
    protected $shortmessageProvider;


    public function __construct(
        Registry $doctrine,
        PushoverNotificationProvider $pushoverProvider,
        EmailNotificationProvider $emailProvider,
        ShortmessageNotificationProvider $shortmessageProvider
    )
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();

        $this->pushoverProvider = $pushoverProvider;
        $this->emailProvider = $emailProvider;
        $this->shortmessageProvider = $shortmessageProvider;
    }

    public function setNotification(AbstractNotification $notification)
    {
        $this->pushoverProvider->setNotification($notification);
        $this->emailProvider->setNotification($notification);

        return $this;
    }

    public function dispatch()
    {
        $subscriptions = $this->entityManager->getRepository('CalderaBundle:NotificationSubscription')->findAll();

        /** @var NotificationSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            if ($subscription->getNotifyByPushover()) {
                $this->pushoverProvider->addUser($subscription->getUser());
            }

            if ($subscription->getNotifyByMail()) {
                $this->emailProvider->addUser($subscription->getUser());
            }

            if ($subscription->getNotifyByShortmessage()) {
                $this->shortmessageProvider->addUser($subscription->getUser());
            }
        }

        return $this;
    }

    public function send()
    {
        $this->pushoverProvider->send();
        $this->emailProvider->send();
        $this->shortmessageProvider->send();

        return $this;
    }
}