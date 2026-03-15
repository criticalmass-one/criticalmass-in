<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Entity\City;

interface FeedItemProviderInterface
{
    /** @return FeedItem[] */
    public function getFeedItemsForCity(City $city, int $page = 1): array;

    /** @return FeedItem[] */
    public function getTimelineItems(
        ?\DateTimeInterface $since = null,
        ?\DateTimeInterface $until = null,
        ?int $limit = null,
    ): array;
}
