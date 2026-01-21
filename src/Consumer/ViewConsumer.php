<?php declare(strict_types=1);

namespace App\Consumer;


class ViewConsumer implements ConsumerInterface
{
    #[\Override]
    public function execute(AMQPMessage $message): int
    {
        $value = $this->serializer->deserialize($message->getBody(), Value::class, 'json');

        $this->persister->persistValues([$value]);

        return self::MSG_ACK;
    }
}
