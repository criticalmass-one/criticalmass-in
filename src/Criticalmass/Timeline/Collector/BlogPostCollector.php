<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\BlogPost;
use App\Criticalmass\Timeline\Item\ThreadItem;

class BlogPostCollector extends AbstractTimelineCollector
{
    protected $entityClass = BlogPost::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var BlogPost $blogPostEntity */
        foreach ($groupedEntities as $blogPostEntity) {
            $item = new ThreadItem();

            $item
                ->setUser($blogPostEntity->getUser())
                ->setBlogPost($blogPostEntity)
                ->setTitle($blogPostEntity->getTitle())
                ->setIntro($blogPostEntity->getIntro())
                ->setDateTime($blogPostEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
