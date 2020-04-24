<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedItemPersister;

use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class RabbitPusher implements FeedItemPersisterInterface
{
    protected ProducerInterface $producer;
    protected SerializerInterface $serializer;

    public function __construct(ProducerInterface $producer, SerializerInterface $serializer)
    {
        $this->producer = $producer;
        $this->serializer = $serializer;
    }

    public function persistFeedItemList(array $feedItemList): FeedItemPersisterInterface
    {
        foreach ($feedItemList as $feedItem) {
            $this->producer->publish($this->serializer->serialize($feedItem, 'json'));
        }

        return $this;
    }
}