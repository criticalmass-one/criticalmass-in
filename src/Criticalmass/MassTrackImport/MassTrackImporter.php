<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

use App\Criticalmass\MassTrackImport\Converter\StravaActivityConverter;
use JMS\Serializer\SerializerInterface;
use Strava\API\Client;
use Strava\API\Service\REST;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MassTrackImporter implements MassTrackImporterInterface
{
    /** @var Client $client */
    protected $client;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var \DateTime $startDateTime */
    protected $startDateTime;

    /** @var \DateTime $endDateTime */
    protected $endDateTime;

    public function __construct(SessionInterface $session, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        $token = $session->get('strava_token');
        $adapter = new \GuzzleHttp\Client(['base_uri' => 'https://www.strava.com/api/v3/']);
        $service = new REST($token, $adapter);
        $this->client = new Client($service);
    }

    public function setStartDateTime(\DateTime $startDateTime): MassTrackImporterInterface
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function setEndDateTime(\DateTime $endDateTime): MassTrackImporterInterface
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function load(): array
    {
        $list = $this->client->getAthleteActivities($this->endDateTime->getTimestamp(), $this->startDateTime->getTimestamp());

        foreach ($list as $key => $activityData) {
            $activity = StravaActivityConverter::convert($activityData);

            $list[$key] = $activity;
        }

        return $list;
    }
}