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
        private readonly int $feedsCacheTtl,
    ) {
    }

    /** @return FeedItem[] */
    public function getFeedItemsForCity(City $city, int $page = 1, bool $refresh = false): array
    {
        $profileIds = $this->getFeedsProfileIdsForCity($city);

        if (empty($profileIds)) {
            return [];
        }

        $cacheKey = sprintf('feeds_city_%d_page_%d', $city->getId(), $page);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($profileIds, $page): array {
            $item->expiresAfter($this->feedsCacheTtl);

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
        }, $refresh ? INF : null);
    }

    /** @return FeedItem[] */
    public function getTimelineItems(
        ?\DateTimeInterface $since = null,
        ?\DateTimeInterface $until = null,
        ?int $limit = null,
        bool $refresh = false,
    ): array {
        // The callers hand in "now" and "a month ago", which would give every
        // request of a new hour its own cache key and thus its own trip to the
        // API. Whole days are the granularity a timeline is read at anyway, so
        // both the query and the key use them — that is what makes the entry
        // survive long enough for the warming cron to keep it hot.
        $since = self::startOfDay($since);
        $until = self::startOfNextDay($until);

        $sinceKey = $since?->format('Y-m-d') ?? 'none';
        $untilKey = $until?->format('Y-m-d') ?? 'none';
        $cacheKey = sprintf('feeds_timeline_%s_%s_%d', $sinceKey, $untilKey, $limit ?? 0);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($since, $until, $limit): array {
            $item->expiresAfter($this->feedsCacheTtl);

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
        }, $refresh ? INF : null);
    }

    public static function startOfDay(?\DateTimeInterface $dateTime): ?\DateTimeImmutable
    {
        return $dateTime === null
            ? null
            : \DateTimeImmutable::createFromInterface($dateTime)->setTime(0, 0);
    }

    /**
     * The upper bound is exclusive in spirit — a timeline for "March" must
     * contain what was posted on the 31st, not stop at its midnight.
     */
    public static function startOfNextDay(?\DateTimeInterface $dateTime): ?\DateTimeImmutable
    {
        return $dateTime === null
            ? null
            : \DateTimeImmutable::createFromInterface($dateTime)->setTime(0, 0)->modify('+1 day');
    }

    /** @return list<int> */
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
