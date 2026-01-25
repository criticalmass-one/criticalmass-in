<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

use App\Criticalmass\MassTrackImport\ActivityLoader\ActivityLoaderInterface;
use App\Criticalmass\MassTrackImport\Converter\StravaActivityConverter;
use App\Entity\User;
use App\Message\ImportTrackCandidateMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MassTrackImporter implements MassTrackImporterInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly ActivityLoaderInterface $activityLoader,
        private readonly TokenStorageInterface $tokenStorage
    ) {
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
        $user = $this->getUser();

        foreach ($modelList as $activityData) {
            $activity = StravaActivityConverter::convert($activityData);

            $message = new ImportTrackCandidateMessage(
                userId: $user->getId(),
                activityId: $activity->getActivityId(),
                name: $activity->getName(),
                distance: $activity->getDistance(),
                elapsedTime: $activity->getElapsedTime(),
                type: $activity->getType(),
                startDateTime: $activity->getStartDateTime(),
                startLatitude: $activity->getStartLatitude(),
                startLongitude: $activity->getStartLongitude(),
                endLatitude: $activity->getEndLatitude(),
                endLongitude: $activity->getEndLongitude(),
                polyline: $activity->getPolyline()
            );

            $this->messageBus->dispatch($message);
        }

        return [];
    }

    protected function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
