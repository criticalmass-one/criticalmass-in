<?php

namespace Caldera\CriticalmassCoreBundle\Utility\Notifications;

/**
 * Die PushoverNotification ist eine Weiterentwicklung aus der BaseNotification
 * und ist wiederum die Basis fuer alle Benachrichtigungen, die ueber den An-
 * bieter Pushover versendet werden koennen.
 */
class PushoverNotification extends BaseNotification
{
	/**
	 * Pushover-Schluessel der Anwendung.
	 */
	protected $token;

	/**
	 * Entitaet des Empfaengers dieser Benachrichtigung.
	 */
	protected $user;

	/**
	 * Spezifisches Geraet des Benutzers, das die Benachrichtigung empfangen soll.
	 */
	protected $device;

	/**
	 * Prioritaet dieser Benachrichtigung. Pushover unterstuetzt Prioritaeten von
	 * -1 bis 2.
	 */
	protected $priority;

	/**
	 * In der Zukunft liegender Zeitpunkt, zu dem die Benachrichtigung angezeigt
	 * werden soll.
	 */
	protected $timestamp;

	/**
	 * Kennzeichen des Geraeusches, das bei der Anzeige der Benachrichtigung abge-
	 * spielt werden soll.
	 */
	protected $sound;

	/**
	 * Legt den Token der Anwendung fest, die diese Notification verschicken soll.
	 *
	 * @param String token: Token der Anwendung
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * Gibt den Token der Anwendung zurueck.
	 *
	 * @return String: Token der Anwendung
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Legt die Beschreibung des Devices fest, auf dem die Notification angezeigt
	 * werden soll.
	 *
	 * @param String $device: Bezeichnung des Geraetes
	 */
	public function setDevice($device)
	{
		$this->device = $device;
	}

	/**
	 * Gibt die Bezeichnung des Geraetes zurueck, auf dem die Benachrichtigung an-
	 * gezeigt werden soll.
	 *
	 * @return String: Bezeichnung des Geraetes
	 */
	public function getDevice()
	{
		return $this->device;
	}

	/**
	 * Legt die Prioritaet dieser Benachrichtigung fest. Pushover unterstuetzt
	 * Werte von -1 bis 2.
	 *
	 * @param Integer $priority: Prioritaet der Benachrichtigung
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * Gibt die Prioritaet der Benachrichtigung zurueck.
	 *
	 * @return Integer: Prioritaet der Benachrichtigung
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * Legt den Zeitstempel fest, zu dem die Benachrichtigung angezeigt werden
	 * soll. Die Angabe erfolgt als UNIX-Timestamp.
	 *
	 * @param Integer $timestamp: Zeitstempel
	 */
	public function setTimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}

	/**
	 * Gibt den Zeitpunkt der Anzeige der Benachrichtigung als Zeichenkette zu-
	 * rueck.
	 *
	 * @return Integer: Zeichenkette des Zeitstempels
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/**
	 * Legt die Bezeichnung des Tones fest, der bei der Anzeige der Benachrichti-
	 * gung abgespielt werden soll.
	 *
	 * @param String $sound: Bezeichnung des Geraeusches
	 */
	public function setSound($sound)
	{
		$this->sound = $sound;
	}

	/**
	 * Gibt die Bezeichnung des Tones zurueck, der beim Anzeigen der Benachrichti-
	 * gung angezeigt wird.
	 *
	 * @return String: Bezeichnung des Geraeusches
	 */
	public function getSound()
	{
		return $this->sound;
	}
}
