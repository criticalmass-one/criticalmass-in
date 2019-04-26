<?php declare(strict_types=1);

namespace App\Consumer;

use App\Criticalmass\ViewStorage\ViewStoragePersisterInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ViewConsumer implements ConsumerInterface
{
    /** @var ViewStoragePersisterInterface $viewSotragePersister */
    protected $viewStoragePersister;

    public function __construct(ViewStoragePersisterInterface $viewStoragePersister)
    {
        $this->viewStoragePersister = $viewStoragePersister;
    }

    public function execute(AMQPMessage $message): int
    {
        $this->viewStoragePersister->persistViews([$message->getBody()]);

        return self::MSG_ACK;
    }
}
