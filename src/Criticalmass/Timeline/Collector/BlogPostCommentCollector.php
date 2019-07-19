<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\Post;
use App\Criticalmass\Timeline\Item\PhotoCommentItem;

class BlogPostCommentCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

    protected function groupEntities(array $postEntities): array
    {
        $groupedEntities = [];

        /** @var Post $postEntity */
        foreach ($postEntities as $postEntity) {
            $userKey = $postEntity->getUser()->getId();
            $blogKey = $postEntity->getBlogPost()->getBlog()->getId();
            $blogPostKey = $postEntity->getBlogPost()->getId();

            if (!array_key_exists($userKey, $groupedEntities) || !is_array($groupedEntities[$userKey])) {
                $groupedEntities[$userKey] = [];
            }

            $groupedEntities[$userKey][$blogKey][$blogPostKey] = $postEntity;
        }

        return $groupedEntities;
    }

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Post $postEntity */
        foreach ($groupedEntities as $userGroup) {
            foreach ($userGroup as $blogGroup) {
                $item = new PhotoCommentItem();

                foreach ($blogGroup as $commentPost) {
                    $item->addPost($commentPost);
                }

                $lastPost = array_pop($blogGroup);

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
        return ['blog'];
    }
}
