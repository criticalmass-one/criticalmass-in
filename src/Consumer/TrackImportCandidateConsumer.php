<?php declare(strict_types=1);

namespace App\Consumer;

use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Entity\TrackImportCandidate;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class TrackImportCandidateConsumer implements ConsumerInterface
{
    public function __construct(protected SerializerInterface $serializer, protected ProposalPersisterInterface $proposalPersister, protected TrackDeciderInterface $trackDecider)
    {
    }

    public function execute(AMQPMessage $message): int
    {
        /** @var TrackImportCandidate $candidate */
        $candidate = $this->serializer->deserialize($message->getBody(), TrackImportCandidate::class, 'json');

        $rideResult = $this->trackDecider->decide($candidate);

        if ($rideResult) {
            $this->proposalPersister->persist($rideResult);
        }

        return self::MSG_ACK;
    }
}
