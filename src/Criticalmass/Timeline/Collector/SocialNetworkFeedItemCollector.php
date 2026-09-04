<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedItemProviderInterface;
use App\Criticalmass\Timeline\Item\SocialNetworkFeedItemItem;
use App\Repository\SocialNetworkProfileRepository;

class SocialNetworkFeedItemCollector implements TimelineCollectorInterface
{
    protected array $items = [];
    protected ?\DateTime $startDateTime = null;
    protected ?\DateTime $endDateTime = null;

    public function __construct(
        private readonly FeedItemProviderInterface $feedItemProvider,
        private readonly SocialNetworkProfileRepository $profileRepository,
    ) {
    }

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineCollectorInterface
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function execute(): TimelineCollectorInterface
    {
        $feedItems = $this->feedItemProvider->getTimelineItems(
            since: $this->startDateTime,
            until: $this->endDateTime,
        );

        $networkIdentifiers = $this->profileRepository->findNetworkIdentifiersByFeedsProfileIds(
            $this->collectProfileIds($feedItems),
        );

        foreach ($feedItems as $feedItem) {
            $item = new SocialNetworkFeedItemItem();
            $item->setFeedItem($feedItem);
            $item->setNetwork($networkIdentifiers[$feedItem->getProfileId()] ?? null);

            $dateTimeString = $item->getDateTime()->format('Y-m-d-H-i-s');
            $itemKey = $dateTimeString . '-' . $item->getUniqId();
            $this->items[$itemKey] = $item;
        }

        return $this;
    }

    /**
     * @param FeedItem[] $feedItems
     * @return list<int>
     */
    private function collectProfileIds(array $feedItems): array
    {
        $profileIds = [];

        foreach ($feedItems as $feedItem) {
            $profileId = $feedItem->getProfileId();

            if ($profileId !== null) {
                $profileIds[$profileId] = $profileId;
            }
        }

        return array_values($profileIds);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getRequiredFeatures(): array
    {
        return [];
    }
}
