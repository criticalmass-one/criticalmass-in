<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Notification\Notification;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Provider\PushoverNotificationProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationController extends AbstractController
{
    public function listAction(Request $request)
    {
        $notification = new Notification();
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
