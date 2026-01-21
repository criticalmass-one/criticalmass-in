<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

use App\Criticalmass\MassTrackImport\ActivityLoader\ActivityLoaderInterface;
use App\Criticalmass\MassTrackImport\Converter\StravaActivityConverter;
use App\Entity\User;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MassTrackImporter implements MassTrackImporterInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly ActivityLoaderInterface $activityLoader,
        private readonly TokenStorageInterface $tokenStorage,
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

        foreach ($modelList as $key => $activityData) {
            $activity = StravaActivityConverter::convert($activityData);

            $activity->setUser($this->getUser());

            $this->messageBus->dispatch($activity);
        }

        return [];
    }

    protected function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
