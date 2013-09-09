<?php

namespace Caldera\CriticalmassBundle\Utility\NotificationPusher;

use \Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Diese Klasse implementiert einen Push-Mechanismus über den Anbieter Push-
 * over.
 */
class PushoverNotificationPusher extends BaseNotificationPusher
{
	/**
	 * Jede App wird mit einem Pushover-Key registriert, der quasi eine Authenti-
	 * fizierung ermoeglicht.
	 */
	protected $pushoverKey;

	/**
	 * Speichert einen Pushover-Key.
	 *
	 * @param String $pushoverKey: Zugangsschluessel der App
	 */
	public function setPushoverKey($pushoverKey)
	{
		$this->pushoverKey = $pushoverKey;
	}

	/**
	 * Implementierung des Versand-Mechanismus. Prinzipiell geht diese Methode nur
	 * die Liste von Benutzern durch und schickt jeden einzelnen ueber Curl eine
	 * Benachrichtigung.
	 */
	public function sendNotification()
	{
		// Benutzerliste durcharbeiten
		foreach ($this->users as $user)
		{
			// Curl-Anfrage zusammensetzen
			curl_setopt_array($ch = curl_init(), array(
				CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
				CURLOPT_POSTFIELDS => array(
					// einzelne Optionen aus der Notification uebertragen
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

			// Abfrage absenden
			curl_exec($ch);
			curl_close($ch);

			// Zaehlerstand inkrementieren
			++$this->notificationsSent;
		}
	}
}
