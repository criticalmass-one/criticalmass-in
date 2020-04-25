<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Consumer;

use App\Entity\SocialNetworkFeedItem;
use OldSound\RabbitMqBundle\RabbitMq\BatchConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class FeedItemBatchConsumer extends AbstractFeedItemConsumer implements BatchConsumerInterface
{
    public function batchExecute(array $messages): array
    {
        $socialNetworkFeedItemList = [];
        $resultList = [];

        /** @var AMQPMessage $message */
        foreach ($messages as $message) {
            $socialNetworkFeedItemList[] = $this->serializer->deserialize($message->getBody(), SocialNetworkFeedItem::class, 'json');

            $resultList[(int)$message->delivery_info['delivery_tag']] = true;
        }

        $this->feedItemPersister->persistFeedItemList($socialNetworkFeedItemList);

        return $resultList;
    }
}
