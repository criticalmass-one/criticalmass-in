<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapElement;

use \Caldera\CriticalmassCoreBundle\Entity\Ride;

class RideMarkerMapElement extends BaseMapElement
{
    protected $ride;

	public function __construct(Ride $ride)
	{
        $this->ride = $ride;
	}

	public function getId()
	{
		return 'ridemarker-'.$this->ride->getLatitude().'-'.$this->ride->getLongitude();
	}

	public function draw()
	{
        $centerPosition = array();

        if ($this->ride->getHasLocation())
        {
            $centerPosition = array('latitude' => $this->ride->getLatitude(), 'longitude' => $this->ride->getLongitude());
        }
        else
        {
            $centerPosition = array('latitude' => $this->ride->getCity()->getLatitude(), 'longitude' => $this->ride->getCity()->getLongitude());
        }

		return array(
			'id' => $this->getId(),
			'type' => 'ridemarker',
			'centerPosition' => $centerPosition,
            'title' => $this->ride->getCity()->getTitle(),
            'location' => $this->ride->getLocation(),
            'hasLocation' => $this->ride->getHasLocation(),
            'date' => $this->ride->getDateTime()->format('d.m.Y'),
            'time' => $this->ride->getDateTime()->format('H:i'),
            'hasTime' => $this->ride->getHasTime(),
            'citySlug' => $this->ride->getCity()->getMainSlug()->getSlug()
			);
	}
}
