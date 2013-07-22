<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

class BaseNotification
{
	protected $token;
	protected $user;
	protected $device;
	protected $priority;
	protected $timestamp;
	protected $sound;

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setDevice($device)
	{
		$this->device = $device;
	}

	public function getDevice()
	{
		return $this->device;
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

	public function getSound()
	{
		return $this->sound;
	}
}