<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\Post;
use App\Criticalmass\Timeline\Item\PhotoCommentItem;

class PhotoCommentCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

    protected function groupEntities(array $postEntities): array
    {
        $groupedEntities = [];

        /** @var Post $postEntity */
        foreach ($postEntities as $postEntity) {
            $userKey = $postEntity->getUser()->getId();
            $rideKey = $postEntity->getPhoto()->getRide()->getId();
            $photoKey = $postEntity->getId();

            if (!array_key_exists($userKey, $groupedEntities) || !is_array($groupedEntities[$userKey])) {
                $groupedEntities[$userKey] = [];
            }

            $groupedEntities[$userKey][$rideKey][$photoKey] = $postEntity;
        }

        return $groupedEntities;
    }

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Post $postEntity */
        foreach ($groupedEntities as $userGroup) {
            foreach ($userGroup as $rideGroup) {
                $item = new PhotoCommentItem();

                foreach ($rideGroup as $commentPost) {
                    $item->addPost($commentPost);
                }

                $lastPost = array_pop($rideGroup);

                $item
                    ->setUser($lastPost->getUser())
                    ->setRide($lastPost->getPhoto()->getRide())
                    ->setDateTime($lastPost->getDateTime());

                $this->addItem($item);
            }
        }

        return $this;
    }

    public function getRequiredFeatures(): array
    {
        return ['photos'];
    }
}
