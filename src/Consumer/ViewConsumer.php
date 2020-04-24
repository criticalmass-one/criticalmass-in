<?php declare(strict_types=1);

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ViewConsumer extends AbstractViewConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $message): int
    {
        $this->viewStoragePersister->persistViews([$message->getBody()]);

        return self::MSG_ACK;
    }
}
