<?php declare(strict_types=1);

namespace App\Criticalmass\Facebook\Api;

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

    public function queryEvents(string $pageId, \DateTime $since, \DateTime $until): ?GraphEdge
    {
        try {
            $endpoint = sprintf('/%s/events?since=%d&until=%d', $pageId, $since->format('U'), $until->format('U'));

            $response = $this->query($endpoint);
        } catch (\Exception $exception) {
            return null;
        }

        try {
            /** @var GraphEdge $graphEdge */
            $graphEdge = $response->getGraphEdge('GraphEvent');

            return $graphEdge;
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function queryEvent(string $eventId, array $fields = []): ?GraphEvent
    {
        $fieldString = implode(',', $this->getQueryFields($fields));

        try {
            $endpoint = sprintf('/%s?fields=%s', $eventId, $fieldString);

            $response = $this->query($endpoint);
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
