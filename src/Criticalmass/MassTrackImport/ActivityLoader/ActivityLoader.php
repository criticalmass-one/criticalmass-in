<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ActivityLoader;

use Strava\API\Client;
use Strava\API\Service\REST;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ActivityLoader implements ActivityLoaderInterface
{
    const PER_PAGE = 100;

    /** @var SessionInterface $session */
    protected $session;

    /** @var \DateTime $startDateTime */
    protected $startDateTime;

    /** @var \DateTime $endDateTime */
    protected $endDateTime;

    /** @var Client $client */
    protected $client;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function setStartDateTime(\DateTime $startDateTime): ActivityLoaderInterface
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function setEndDateTime(\DateTime $endDateTime): ActivityLoaderInterface
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function load(): array
    {
        $token = $this->session->get('strava_token');
        $adapter = new \GuzzleHttp\Client(['base_uri' => 'https://www.strava.com/api/v3/']);
        $service = new REST($token, $adapter);
        $this->client = new Client($service);

        $activityList = [];
        $pageNumber = 1;

        do {
            $results = $this->client->getAthleteActivities($this->endDateTime->getTimestamp(), $this->startDateTime->getTimestamp(), $pageNumber, 100);

            if (is_array($results)) {
                $activityList = array_merge($activityList, $results);

                ++$pageNumber;
            }
        } while (is_array($results) && count($results) !== 0);
        
        return $activityList;
    }
}
