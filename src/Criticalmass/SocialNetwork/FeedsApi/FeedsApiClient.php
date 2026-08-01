<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Profile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FeedsApiClient implements FeedsApiClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $feedsApiUrl,
        private readonly string $feedsApiToken,
    ) {
    }

    /** @return Network[] */
    public function getNetworks(): array
    {
        $data = $this->request('GET', '/api/networks');

        return array_map(
            fn(array $item) => Network::fromApiResponse($item),
            $data['member'] ?? $data
        );
    }

    public function createProfile(string $identifier, string $networkIdentifier): Profile
    {
        $networks = $this->getNetworks();
        $networkIri = null;

        foreach ($networks as $network) {
            if ($network->getIdentifier() === $networkIdentifier) {
                $networkIri = $network->getIri();
                break;
            }
        }

        if (!$networkIri) {
            throw new \RuntimeException(sprintf('Network "%s" not found in Feeds API', $networkIdentifier));
        }

        $data = $this->request('POST', '/api/profiles', [
            'identifier' => $identifier,
            'network' => $networkIri,
            'autoFetch' => true,
        ]);

        return Profile::fromApiResponse($data);
    }

    public function deleteProfile(int $feedsProfileId): void
    {
        $this->request('DELETE', sprintf('/api/profiles/%d', $feedsProfileId));
    }

    public function getProfile(int $feedsProfileId): ?Profile
    {
        try {
            $data = $this->request('GET', sprintf('/api/profiles/%d', $feedsProfileId));

            return Profile::fromApiResponse($data);
        } catch (\Exception) {
            return null;
        }
    }

    /** @return Profile[] */
    public function getProfiles(?string $networkIdentifier = null): array
    {
        $query = [];

        if ($networkIdentifier) {
            $query['network'] = $networkIdentifier;
        }

        $data = $this->request('GET', '/api/profiles', queryParams: $query);

        return array_map(
            fn(array $item) => Profile::fromApiResponse($item),
            $data['member'] ?? $data
        );
    }

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
    ): array {
        $query = [];

        if ($limit) {
            $query['limit'] = $limit;
        }

        if ($since) {
            $query['since'] = $since->format(\DateTimeInterface::ATOM);
        }

        if ($until) {
            $query['until'] = $until->format(\DateTimeInterface::ATOM);
        }

        if ($networkIdentifier) {
            $query['network'] = $networkIdentifier;
        }

        if ($profileIds) {
            $query['profile'] = $profileIds;
        }

        $query['order[dateTime]'] = $orderDirection;

        $data = $this->request('GET', '/api/timeline', queryParams: $query);

        return array_map(
            fn(array $item) => FeedItem::fromApiResponse($item),
            $data['member'] ?? $data
        );
    }

    /** @return FeedItem[] */
    public function getItems(
        ?int $profileId = null,
        int $page = 1,
        string $orderDirection = 'desc',
    ): array {
        $query = [
            'page' => $page,
            'order[dateTime]' => $orderDirection,
        ];

        if ($profileId) {
            $query['profile'] = $profileId;
        }

        $data = $this->request('GET', '/api/items', queryParams: $query);

        return array_map(
            fn(array $item) => FeedItem::fromApiResponse($item),
            $data['member'] ?? $data
        );
    }

    private function request(string $method, string $path, ?array $body = null, array $queryParams = []): ?array
    {
        $options = [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->feedsApiToken),
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json',
            ],
        ];

        if ($body !== null) {
            $options['json'] = $body;
        }

        $url = rtrim($this->feedsApiUrl, '/') . $path;

        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        $response = $this->httpClient->request($method, $url, $options);
        $statusCode = $response->getStatusCode();

        if ($statusCode === 204) {
            return null;
        }

        if ($statusCode >= 400) {
            throw new \RuntimeException(sprintf('Feeds API returned status %d for %s %s', $statusCode, $method, $path));
        }

        return $response->toArray();
    }
}
