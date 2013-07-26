<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class PushoverNotificationPusher extends BaseNotificationPusher
{
	protected $pushoverKey;

	public function setPushoverKey($pushoverKey)
	{
		$this->pushoverKey = $pushoverKey;
	}

	public function sendNotification()
	{
		foreach ($this->users as $user)
		{
			curl_setopt_array($ch = curl_init(), array(
				CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
				CURLOPT_POSTFIELDS => array(
					'token' => $this->pushoverKey,
					'user' => $user->getPushoverKey(),
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

			++$this->notificationsSent;
		}
	}
}
