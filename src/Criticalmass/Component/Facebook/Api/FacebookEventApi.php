<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook\Api;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Utils\DateTimeUtils;
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

    public function getEventForCityMonth(City $city, \DateTime $month): ?GraphEvent
    {
        $pageId = $this->getCityPageId($city);
        $since = DateTimeUtils::getMonthStartDateTime($month)->format('U');
        $until = DateTimeUtils::getMonthEndDateTime($month)->format('U');

        return $this->queryEvents($pageId, $since, $until);
    }

    public function getEventForRide(Ride $ride): ?GraphEvent
    {
        if ($ride->getFacebook()) {
            $eventId = $this->getRideEventId($ride);

            $event = $this->queryEvent($eventId, $this->standardFields);

            return $event;
        }

        $pageId = $this->getCityPageId($ride->getCity());

        if (!$pageId) {
            return null;
        }

        $since = DateTimeUtils::getMonthStartDateTime($ride->getDateTime())->format('U');
        $until = DateTimeUtils::getMonthEndDateTime($ride->getDateTime())->format('U');

        return $this->queryEvents($pageId, $since, $until);
    }

    protected function queryEvents($pageId, $since, $until): ?GraphEvent
    {
        try {
            $endpoint = sprintf('/%s/events?since=%d&d&until=%d', $pageId, $since, $until);

            $response = $this->facebook->get($endpoint);
        } catch(\Exception $exception) {
            return null;
        }

        try {
            /** @var GraphEdge $eventEdge */
            $eventEdge = $response->getGraphEdge('GraphEvent');
        } catch (\Exception $e) {
            return null;
        }

        /** @var GraphEvent $event */
        $event = null;

        foreach ($eventEdge as $event) {
        }

        return $event;
    }

    protected function queryEvent(string $eventId, array $fields = []): ?GraphEvent
    {
        $fieldString = implode(',', $fields);

        try {
            $endpoint = sprintf('/%s?fields=%s', $eventId, $fieldString);

            $response = $this->facebook->get($endpoint);
        } catch (\Exception $e) {
            return null;
        }

        try {
            /** @var GraphEvent $event */
            $event = $response->getGraphEvent();
        } catch (\Exception $e) {
            return null;
        }

        return $event;
    }
}
