<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

interface BaseNotification
{
	private $message;
	private $device;
	private $title;
	private $url;
	private $url_title;
	private $priority;
	private $timestamp;
	private $sound;
}