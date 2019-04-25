<?php declare(strict_types=1);

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ValueConsumer implements ConsumerInterface
{
    /**
     * @var ViewStoragePersisterInterface $viewSotragePersister
     */
    protected $viewStoragePersister;

    public function __construct(ViewStoragePersisterInterface $viewStoragePersister)
    {
        $this->viewStoragePersister = $viewStoragePersister;

        parent::__construct();
    }

    public function execute(AMQPMessage $message): int
    {
        $value = unserialize($message->getBody());
        
        $this->persister->persistValues([$value]);

        return self::MSG_ACK;
    }
}
