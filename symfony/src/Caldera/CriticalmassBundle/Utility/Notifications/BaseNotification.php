<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

class BaseNotification
{
	private $message;
	private $device;
	private $title;
	private $url;
	private $urlTitle;
	private $priority;
	private $timestamp;
	private $sound;

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function setDevice($device)
	{
		$this->device = $device;
	}

	public function getDevice()
	{
		return $this->device;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setUrlTitle($urlTitle)
	{
		$this->urlTitle = $urlTitle;
	}

	public function getUrlTitle()
	{
		return $this->urlTitle;
	}

	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	public function getPriority()
	{
		return $this->priority;
	}

	public function setTimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}

	public function getTimestamp()
	{
		return $this->timestamp;
	}

	public function setSound($sound)
	{
		$this->sound = $sound;
	}

	public function getSound($sound)
	{
		return $this->sound;
	}

}