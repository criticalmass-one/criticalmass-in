<?php

namespace Caldera\CriticalmassBundle\Utility\Notifications;

use Caldera\CriticalmassBundle\Entity\Ride as Ride;

class RideAnnouncementNotification extends BaseNotification
{
	public function __construct(Ride $ride)
	{
		$this->setMessage('Nächste Tour: '.$ride->getDate());
		$this->setTitle($ride->getCity()->getTitle());
		$this->setUrl('http://www.criticalmass.in/'.$ride->getCity()->getMainSlug()->getSlug());
		$this->setUrlTitle('criticalmass.in/'.$ride->getCity()->getMainSlug()->getSlug());
		$this->setPriority(0);
		$this->setSound('bike');
	}
}
