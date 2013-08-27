<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

use Caldera\CriticalmassBundle\Entity\Ride as Ride;

/**
 * Diese Benachrichtigung informiert den Benutzer ueber die Veroeffentlichung
 * einer neuen Tour.
 */
class RideAnnouncementNotification extends PushoverNotification
{
	/**
	 * Im Konstruktor werden die Eigenschaften dieser Benachrichtigung festgelegt.
	 *
	 * @param Ride $ride: Ride-Entitaet, aus der die Eigenschaften ausgelesen wer-
	 * den koennen.
	 */
	public function __construct(Ride $ride)
	{
		$this->setMessage('Nächste Tour: '.$ride->getDate()->format('d.m.Y'));
		$this->setTitle($ride->getCity()->getTitle());
		$this->setUrl('http://www.criticalmass.in/'.$ride->getCity()->getMainSlug()->getSlug());
		$this->setUrlTitle('criticalmass.in/'.$ride->getCity()->getMainSlug()->getSlug());
		$this->setPriority(0);
		$this->setSound('bike');
	}
}
