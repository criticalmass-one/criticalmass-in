<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ActivityLoader;

use Carbon\Carbon;
use Strava\API\Client;
use Strava\API\Service\REST;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Psr18Client;

class ActivityLoader implements ActivityLoaderInterface
{
    const PER_PAGE = 100;

    protected Carbon $startDateTime;
    protected Carbon $endDateTime;
    protected Client $client;

    public function __construct(
        private readonly SessionInterface $session,
        private readonly HttpClientInterface $httpClient
    )
    {

    }

    public function setStartDateTime(Carbon $startDateTime): ActivityLoaderInterface
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function setEndDateTime(Carbon $endDateTime): ActivityLoaderInterface
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function load(): array
    {
        $token = $this->session->get('strava_token');

        $psr18Client = new Psr18Client($this->httpClient);

        $service = new REST($token, $psr18Client);
        $this->client = new Client($service);

        $activityList = [];
        $pageNumber = 1;

        do {
            $results = $this->client->getAthleteActivities(
                $this->endDateTime->getTimestamp(),
                $this->startDateTime->getTimestamp(),
                $pageNumber,
                self::PER_PAGE
            );

            if (is_array($results)) {
                $activityList = array_merge($activityList, $results);
                ++$pageNumber;
            }
        } while (is_array($results) && count($results) !== 0);

        return $activityList;
    }
}
