<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Consumer;

use App\Criticalmass\SocialNetwork\FeedItemPersister\NonDuplicatesFeedItemPersister;
use JMS\Serializer\SerializerInterface;

abstract class AbstractFeedItemConsumer
{
    protected SerializerInterface $serializer;
    protected NonDuplicatesFeedItemPersister $feedItemPersister;

    public function __construct(SerializerInterface $serializer, NonDuplicatesFeedItemPersister $feedItemPersister)
    {
        $this->serializer = $serializer;
        $this->feedItemPersister = $feedItemPersister;
    }
}
