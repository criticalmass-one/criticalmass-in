<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\BlogPostItem;
use App\Entity\BlogPost;

class BlogPostCollector extends AbstractTimelineCollector
{
    protected $entityClass = BlogPost::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var BlogPost $blogPostEntity */
        foreach ($groupedEntities as $blogPostEntity) {
            $item = new BlogPostItem();

            $item
                ->setBlogPost($blogPostEntity)
                ->setTitle($blogPostEntity->getTitle())
                ->setIntro($blogPostEntity->getIntro())
                ->setUser($blogPostEntity->getUser())
                ->setDateTime($blogPostEntity->getCreatedAt());

            $this->addItem($item);
        }

        return $this;
    }
}
