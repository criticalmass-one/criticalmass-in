<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

use App\Criticalmass\MassTrackImport\ActivityLoader\ActivityLoaderInterface;
use App\Criticalmass\MassTrackImport\Converter\StravaActivityConverter;
use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use JMS\Serializer\SerializerInterface;

class MassTrackImporter implements MassTrackImporterInterface
{
    /** @var ActivityLoaderInterface $activityLoader */
    protected $activityLoader;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var TrackDeciderInterface $trackDecider */
    protected $trackDecider;

    /** @var ProposalPersisterInterface $proposalPersister */
    protected $proposalPersister;

    public function __construct(ProposalPersisterInterface $proposalPersister, SerializerInterface $serializer, TrackDeciderInterface $trackDecider, ActivityLoaderInterface $activityLoader)
    {
        $this->serializer = $serializer;
        $this->trackDecider = $trackDecider;
        $this->activityLoader = $activityLoader;
        $this->proposalPersister = $proposalPersister;
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
                $resultList[$result->getActivity()->getActivityId()] = $result;
            }
        }

        $this->proposalPersister->persist($resultList);

        return $resultList;
    }
}
