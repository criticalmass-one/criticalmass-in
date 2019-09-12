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
    /** @var ActivityLoaderInterface $activityLoader */
    protected $activityLoader;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var TrackDeciderInterface $trackDecider */
    protected $trackDecider;

    /** @var ProposalPersisterInterface $proposalPersister */
    protected $proposalPersister;

    /** @var ProducerInterface $producer */
    protected $producer;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(ProducerInterface $producer, ProposalPersisterInterface $proposalPersister, SerializerInterface $serializer, TrackDeciderInterface $trackDecider, ActivityLoaderInterface $activityLoader, TokenStorageInterface $tokenStorage)
    {
        $this->serializer = $serializer;
        $this->trackDecider = $trackDecider;
        $this->activityLoader = $activityLoader;
        $this->proposalPersister = $proposalPersister;
        $this->producer = $producer;
        $this->tokenStorage = $tokenStorage;
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
