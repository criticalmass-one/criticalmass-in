<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport;

use App\Criticalmass\MassTrackImport\ActivityLoader\ActivityLoaderInterface;
use App\Criticalmass\MassTrackImport\Converter\StravaActivityConverter;
use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MassTrackImporter implements MassTrackImporterInterface
{
    public function __construct(protected ProducerInterface $producer, protected ProposalPersisterInterface $proposalPersister, protected SerializerInterface $serializer, protected TrackDeciderInterface $trackDecider, protected ActivityLoaderInterface $activityLoader, protected TokenStorageInterface $tokenStorage)
    {
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

            $serializedActivity = $this->serializer->serialize($activity, 'json');

            $this->producer->publish($serializedActivity);
        }

        return [];
    }

    protected function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
