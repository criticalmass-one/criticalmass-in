<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

class BaseNotification
{
	protected $message;
	protected $title;
	protected $url;
	protected $urlTitle;

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getMessage()
	{
		return $this->message;
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
}