<?php declare(strict_types=1);

namespace App\Consumer;

use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class TrackImportCandidateConsumer implements ConsumerInterface
{
    /** @var ProposalPersisterInterface $proposalPersister */
    protected $proposalPersister;

    /** @var TrackDeciderInterface $trackDecider */
    protected $trackDecider;

    public function __construct(ProposalPersisterInterface $proposalPersister, TrackDeciderInterface $trackDecider)
    {
        $this->proposalPersister = $proposalPersister;
        $this->trackDecider = $trackDecider;
    }

    public function execute(AMQPMessage $message): int
    {
        $model = $message->getBody();
        $result = $this->trackDecider->decide($model);

        $this->proposalPersister->persist([$result]);

        return self::MSG_ACK;
    }
}
