<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

/**
 * Die BaseNotification stellt die Basis einer beliebigen Benachrichtigung dar.
 * Aus dieser Klasse werden alle weiteren Benachrichtungen abgeleitet.
 */
class BaseNotification
{
	/**
	 * Benachrichtigungstext der Notification.
	 */
	protected $message;

	/**
	 * Titel der Notification.
	 */
	protected $title;

	/**
	 * Verlinkte Adresse der Notification.
	 */
	protected $url;

	/**
	 * Aufschrift der verlinkten Adresse der Benachrichtung.
	 */
	protected $urlTitle;

	/**
	 * Beschreibt den Benachrichtigungstext der Notification.
	 *
	 * @param String $message: Text der Benachrichtung
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * Gibt den Benachrichtigungstext der Notification zurueck.
	 *
	 * @return String: Text der Benachrichtung
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Beschreibt den Titel der Notification.
	 *
	 * @param String $title: Titel der Benachrichtung
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Gibt den Titel der Notification zurueck.
	 *
	 * @return String: Titel der Benachrichtung
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Beschreibt die verlinkte Adresse der Notification.
	 *
	 * @param String $url: Verlinkte Adresse der Benachrichtung
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Gibt die verlinkte Adresse der Notification zurueck.
	 *
	 * @return String: Verlinkte Adresse der Benachrichtung
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Beschreibt den Titel der verlinkten Adresse der Notification.
	 *
	 * @param String $urlTitle: Titel der verlinkten ADresseder Benachrichtung
	 */
	public function setUrlTitle($urlTitle)
	{
		$this->urlTitle = $urlTitle;
	}

	/**
	 * Gibt den Titel der verlinkten Adresse der Notification zurueck.
	 *
	 * @return String: Titel der verlinkten Adresse der Benachrichtung
	 */
	public function getUrlTitle()
	{
		return $this->urlTitle;
	}
}