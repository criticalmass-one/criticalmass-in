<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

abstract class BaseNotificationPusher
{
	protected $notification;
	protected $users = array();
	protected $notificationsSent = 0;

	public function __construct(Notifications\BaseNotification $notification, $users)
	{
		$this->notification = $notification;
		$this->users = $users;
	}

	public abstract function sendNotification();

	public function getSentNotifications()
	{
		return $this->notificationsSent;
	}
}