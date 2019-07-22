<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\BlogPostCommentItem;
use App\Entity\Post;

class BlogPostCommentCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Post $postEntity */
        foreach ($groupedEntities as $postEntity) {
            $item = new BlogPostCommentItem();

            $item
                ->setBlogPost($postEntity->getBlogPost())
                ->setBlogPostTitle($postEntity->getBlogPost()->getTitle())
                ->setText($postEntity->getMessage())
                ->setUser($postEntity->getUser())
                ->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }

    public function getRequiredFeatures(): array
    {
        return ['blog'];
    }
}
