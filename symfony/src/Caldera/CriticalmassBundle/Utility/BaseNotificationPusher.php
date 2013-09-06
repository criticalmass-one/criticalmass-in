<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Repraesentiert die Basis eines Notification-Pushers, also einer Mechanik,
 * die fuer die Zustellung von Push-Nachrichten zu den einzelnen Benutzern zu-
 * staendig ist.
 */
abstract class BaseNotificationPusher
{
	/**
	 * Speichert die Instanz einer Push-Benachrichtigung.
	 */
	protected $notification;

	/**
	 * Liste von Benutzern, die eine Benachrichtigung bekommen sollen.
	 */
	protected $users = array();

	/**
	 * Anzahl der verschickten Benachrichtigungen.
	 */
	protected $notificationsSent = 0;

	/**
	 * Konstruiert einen NotificationPusher. Als Parameter wird die zu ver-
	 * schickende Nachricht und eine Liste von anzuschreibenden Benutzern erwar-
	 * tet.
	 *
	 * @param Notifications\BaseNotification $notification: Zu verschickende Nach-
	 * richt
	 * @param $users: Liste von anzuschriebenden Benutzern
	 */
	public function __construct(Notifications\BaseNotification $notification, $users)
	{
		$this->notification = $notification;
		$this->users = $users;
	}

	/**
	 * Diese Methode muss implementiert werden, um Nachrichten zu verschicken. Die
	 * jeweilige Implementierng haengt von der API des jeweiligen Anbieters ab.
	 */
	public abstract function sendNotification();

	/**
	 * Gibt die Anzahl der verschickten Nachrichten zurueack. Dazu muss beim Ver-
	 * sand der Nachrichten die Eigenschaft notificationsSent inkrementiert wer-
	 * den.
	 *
	 * @return Integer: Anzahl der versendeten Nachrichten
	 */
	public function getSentNotifications()
	{
		return $this->notificationsSent;
	}
}