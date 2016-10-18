<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Facebook;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassCoreBundle\Utils\DateTimeUtils;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;

class FacebookEventApi extends FacebookApi
{
    protected $standardFields = [
        'name',
        'description',
        'attending_count',
        'maybe_count',
        'declined_count',
        'interested_count',
        'noreply_count',
        'start_time',
        'end_time',
        'updated_time',
        'place'
    ];

    public function getEventForCityMonth(City $city, \DateTime $month)
    {
        $pageId = $this->getCityPageId($city);
        $since = DateTimeUtils::getMonthStartDateTime($month)->format('U');
        $until = DateTimeUtils::getMonthEndDateTime($month)->format('U');

        return $this->queryEvents($pageId, $since, $until);
    }

    public function getEventForRide(Ride $ride)
    {
        if ($ride->getFacebook()) {
            $eventId = $this->getRideEventId($ride);

            $event = $this->queryEvent($eventId, $this->standardFields);

            return $event;
        }

        $pageId = $this->getCityPageId($ride->getCity());
        $since = DateTimeUtils::getMonthStartDateTime($ride->getDateTime())->format('U');
        $until = DateTimeUtils::getMonthEndDateTime($ride->getDateTime())->format('U');

        return $this->queryEvents($pageId, $since, $until);
    }

    protected function queryEvents($pageId, $since, $until)
    {
        try {
            $response = $this->facebook->get('/' . $pageId . '/events?since=' . $since . '&until=' . $until);
        } catch (\Exception $e) {
            return null;
        }

        try {
            /**
             * @var GraphEdge $eventEdge
             */
            $eventEdge = $response->getGraphEdge('GraphEvent');
        } catch (\Exception $e) {
            return null;
        }

        /**
         * @var GraphEvent $event
         */
        $event = null;

        foreach ($eventEdge as $event) {
        }

        return $event;
    }

    protected function queryEvent($eventId, array $fields = [])
    {
        $fieldString = implode(',', $fields);

        try {
            $response = $this->facebook->get('/'.$eventId.'?fields='.$fieldString);
        } catch (\Exception $e) {
            return null;
        }

        try {
            /**
             * @var GraphEvent $event
             */
            $event = $response->getGraphEvent();
        } catch (\Exception $e) {
            return null;
        }

        return $event;
    }
}