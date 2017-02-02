<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Notification\Notification;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends AbstractController
{
    public function listAction(Request $request)
    {
        $notification = new Notification\RideLocationPublishedNotification();
        $notification->setTitle('Treffpunkt veröffentlicht');
        $notification->setMessage('Der Treffpunkt ist am Jungfernstieg');
        $notification->setShortMessage('Der Treffpunkt der Critical Mass Hamburg am 1. Januar 2016 ist um 14 Uhr am Jungfernstieg');
        $notification->setCreationDateTime(new \DateTime());

        $notificationDispatcher = $this->get('caldera.criticalmass.notification.dispatcher');
        $notificationDispatcher->setNotification($notification);
        $notificationDispatcher->dispatch();
        $notificationDispatcher->send();

        $notifications = $this->getNotificationSubscriptionRepository()->findAll();

        return $this->render(
            'CalderaCriticalmassSiteBundle:NotificationSubscription:list.html.twig',
            [
                'notifications' => $notifications
            ]
        );
    }
}
