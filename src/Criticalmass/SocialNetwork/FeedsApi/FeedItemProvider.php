<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Entity\City;
use App\Repository\SocialNetworkProfileRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FeedItemProvider implements FeedItemProviderInterface
{
    public function __construct(
        private readonly FeedsApiClientInterface $feedsApiClient,
        private readonly SocialNetworkProfileRepository $profileRepository,
        private readonly CacheInterface $cache,
    ) {
    }

    /** @return FeedItem[] */
    public function getFeedItemsForCity(City $city, int $page = 1): array
    {
        $profileIds = $this->getFeedsProfileIdsForCity($city);

        if (empty($profileIds)) {
            return [];
        }

        $cacheKey = sprintf('feeds_city_%d_page_%d', $city->getId(), $page);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($profileIds, $page): array {
            $item->expiresAfter(300);

            try {
                // One request for all of the city's profiles: the API merges,
                // orders and paginates them, so a city with ten profiles costs
                // one round trip and yields one page of items — not ten pages
                // stacked on top of each other.
                return $this->feedsApiClient->getItems(
                    profileIds: $profileIds,
                    page: $page,
                    orderDirection: 'desc',
                );
            } catch (\Throwable) {
                // The Feeds API being unavailable must not take the page down:
                // show no social items rather than a 500.
                return [];
            }
        });
    }

    /** @return FeedItem[] */
    public function getTimelineItems(
        ?\DateTimeInterface $since = null,
        ?\DateTimeInterface $until = null,
        ?int $limit = null,
    ): array {
        $sinceKey = $since ? $since->format('Y-m-d-H') : 'none';
        $untilKey = $until ? $until->format('Y-m-d-H') : 'none';
        $cacheKey = sprintf('feeds_timeline_%s_%s_%d', $sinceKey, $untilKey, $limit ?? 0);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($since, $until, $limit): array {
            $item->expiresAfter(300);

            try {
                return $this->feedsApiClient->getTimelineItems(
                    limit: $limit,
                    since: $since,
                    until: $until,
                    orderDirection: 'desc',
                );
            } catch (\Throwable) {
                // A Feeds API outage must not break the timeline/home page.
                return [];
            }
        });
    }

    /** @return int[] */
    private function getFeedsProfileIdsForCity(City $city): array
    {
        $profiles = $this->profileRepository->findByCity($city);

        $ids = [];

        foreach ($profiles as $profile) {
            if ($profile->getFeedsProfileId()) {
                $ids[] = $profile->getFeedsProfileId();
            }
        }

        return $ids;
    }
}
