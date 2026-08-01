<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Profile;

interface FeedsApiClientInterface
{
    /** @return Network[] */
    public function getNetworks(): array;

    public function createProfile(string $identifier, string $networkIdentifier): Profile;

    public function deleteProfile(int $feedsProfileId): void;

    public function getProfile(int $feedsProfileId): ?Profile;

    /** @return Profile[] */
    public function getProfiles(?string $networkIdentifier = null): array;

    /**
     * @param int[]|null $profileIds
     * @return FeedItem[]
     */
    public function getTimelineItems(
        ?int $limit = null,
        ?\DateTimeInterface $since = null,
        ?\DateTimeInterface $until = null,
        ?string $networkIdentifier = null,
        ?array $profileIds = null,
        string $orderDirection = 'desc',
    ): array;

    /**
     * @return FeedItem[]
     */
    public function getItems(
        ?int $profileId = null,
        int $page = 1,
        string $orderDirection = 'desc',
    ): array;
}
