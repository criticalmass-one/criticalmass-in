<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class NotificationPusher
{
	private $notification;

	public function __construct(Notifications\BaseNotification $notification)
	{
		$this->notification = $notification;
	}
/*	"token" => "wP7MBPTf5TFvazDCtWf2mL1eH9m1fK",
	"user" => "Gb1whEAd6G1mUxPccfgHRJWYYAnrxh",*/
	public function sendNotification()
	{
		curl_setopt_array($ch = curl_init(), array(
			CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
			CURLOPT_POSTFIELDS => array(
				'token' => $this->notification->getToken(),
				'user' => $this->notification->getUser(),
				'message' => $this->notification->getMessage(),
				'device' => $this->notification->getDevice(),
				'title' => $this->notification->getTitle(),
				'url' => $this->notification->getUrl(),
				'url_title' => $this->notification->getUrlTitle(),
				'priority' => $this->notification->getPriority(),
				'timestamp' => $this->notification->getTimeStamp(),
				'sound' => $this->notification->getSound()
		)));

		curl_exec($ch);
		curl_close($ch);
	}
}