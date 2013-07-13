<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class NotificationPusher
{
	public function __construct()
	{
		
	}

	public function sendNotification()
	{
		curl_setopt_array($ch = curl_init(), array(
			CURLOPT_URL => "https://api.pushover.net/1/messages.json",
			CURLOPT_POSTFIELDS => array(
				"token" => "wP7MBPTf5TFvazDCtWf2mL1eH9m1fK",
				"user" => "Gb1whEAd6G1mUxPccfgHRJWYYAnrxh",
				"message" => "hello world",
		)));

		curl_exec($ch);
		curl_close($ch);
	}
}