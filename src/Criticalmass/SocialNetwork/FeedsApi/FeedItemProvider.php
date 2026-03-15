<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use App\Repository\SocialNetworkProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FeedItemProvider implements FeedItemProviderInterface
{
    public function __construct(
        private readonly FeedsApiClientInterface $feedsApiClient,
        private readonly ManagerRegistry $managerRegistry,
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

            $allItems = [];

            foreach ($profileIds as $profileId) {
                $items = $this->feedsApiClient->getItems(
                    profileId: $profileId,
                    page: $page,
                    orderDirection: 'desc',
                );

                $allItems = array_merge($allItems, $items);
            }

            usort($allItems, fn(FeedItem $a, FeedItem $b) => $b->getDateTime() <=> $a->getDateTime());

            return $allItems;
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

            return $this->feedsApiClient->getTimelineItems(
                limit: $limit,
                since: $since,
                until: $until,
                orderDirection: 'desc',
            );
        });
    }

    /** @return int[] */
    private function getFeedsProfileIdsForCity(City $city): array
    {
        /** @var SocialNetworkProfileRepository $repository */
        $repository = $this->managerRegistry->getRepository(SocialNetworkProfile::class);
        $profiles = $repository->findByCity($city);

        $ids = [];

        foreach ($profiles as $profile) {
            if ($profile->getFeedsProfileId()) {
                $ids[] = $profile->getFeedsProfileId();
            }
        }

        return $ids;
    }
}
