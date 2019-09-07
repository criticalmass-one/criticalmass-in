<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

use App\Criticalmass\MassTrackImport\ActivityLoader\ActivityLoaderInterface;
use App\Criticalmass\MassTrackImport\Converter\StravaActivityConverter;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MassTrackImporter implements MassTrackImporterInterface
{
    /** @var ActivityLoaderInterface $activityLoader */
    protected $activityLoader;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var TrackDeciderInterface $trackDecider */
    protected $trackDecider;

    public function __construct(SessionInterface $session, SerializerInterface $serializer, TrackDeciderInterface $trackDecider, ActivityLoaderInterface $activityLoader)
    {
        $this->serializer = $serializer;
        $this->trackDecider = $trackDecider;
        $this->activityLoader = $activityLoader;
    }

    public function setStartDateTime(\DateTime $startDateTime): MassTrackImporterInterface
    {
        $this->activityLoader->setStartDateTime($startDateTime);

        return $this;
    }

    public function setEndDateTime(\DateTime $endDateTime): MassTrackImporterInterface
    {
        $this->activityLoader->setEndDateTime($endDateTime);

        return $this;
    }

    public function execute(): array
    {
        $modelList = $this->activityLoader->load();

        foreach ($modelList as $key => $activityData) {
            $activity = StravaActivityConverter::convert($activityData);

            $modelList[$key] = $activity;
        }

        $resultList = [];

        foreach ($modelList as $model) {
            if ($result = $this->trackDecider->decide($model)) {
                $resultList[] = $result;
            }
        }

        return $resultList;
    }
}
