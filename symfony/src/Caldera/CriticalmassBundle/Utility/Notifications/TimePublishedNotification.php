<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

use Caldera\CriticalmassBundle\Entity\Ride as Ride;

class TimePublishedNotification extends BaseNotification
{
	public function __construct(Ride $ride)
	{
		$this->setMessage('Die Tour am '.$ride->getDate()->format('d.m.Y').' beginnt um '.$ride->getTime()->format('H:i').'Uhr');
		$this->setTitle($ride->getCity()->getTitle());
		$this->setUrl('http://www.criticalmass.in/'.$ride->getCity()->getMainSlug()->getSlug());
		$this->setUrlTitle('criticalmass.in/'.$ride->getCity()->getMainSlug()->getSlug());
		$this->setPriority(0);
		$this->setSound('bike');
	}
}
