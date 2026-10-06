<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\StatusPostItem;
use App\Entity\Post;

class StatusPostCollector extends AbstractTimelineCollector
{
    protected string $entityClass = Post::class;

    /**
     * @param Post[] $groupedEntities
     */
    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        foreach ($groupedEntities as $postEntity) {
            $item = (new StatusPostItem())->setPost($postEntity);

            $item
                ->setUser($postEntity->getUser())
                ->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRequiredFeatures(): array
    {
        return ['status_posts'];
    }
}
