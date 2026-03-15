<?php declare(strict_types=1);

namespace Tests\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Profile;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedsApiClient;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedsApiClientInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[TestDox('FeedsApiClient')]
class FeedsApiClientTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClient;
    private FeedsApiClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = new FeedsApiClient(
            $this->httpClient,
            'https://feeds.example.com',
            'test-token-123',
        );
    }

    private function createMockResponse(int $statusCode, array $body): ResponseInterface&MockObject
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('toArray')->willReturn($body);

        return $response;
    }

    #[TestDox('implements FeedsApiClientInterface')]
    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(FeedsApiClientInterface::class, $this->client);
    }

    #[TestDox('getNetworks returns array of Network DTOs')]
    public function testGetNetworks(): void
    {
        $response = $this->createMockResponse(200, [
            'member' => [
                [
                    'id' => 108,
                    'identifier' => 'twitter',
                    'name' => 'Twitter',
                    'icon' => 'fab fa-twitter',
                    'backgroundColor' => 'rgb(29, 161, 242)',
                    'textColor' => 'white',
                    'profileUrlPattern' => '#^https?://twitter\.com/.+$#i',
                ],
            ],
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $networks = $this->client->getNetworks();

        $this->assertCount(1, $networks);
        $this->assertInstanceOf(Network::class, $networks[0]);
        $this->assertEquals('twitter', $networks[0]->getIdentifier());
    }

    #[TestDox('sends Bearer token in Authorization header')]
    public function testSendsAuthorizationHeader(): void
    {
        $response = $this->createMockResponse(200, ['member' => []]);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('https://feeds.example.com/api/networks'),
                $this->callback(function (array $options): bool {
                    return isset($options['headers']['Authorization'])
                        && $options['headers']['Authorization'] === 'Bearer test-token-123';
                })
            )
            ->willReturn($response);

        $this->client->getNetworks();
    }

    #[TestDox('deleteProfile sends DELETE request')]
    public function testDeleteProfile(): void
    {
        $response = $this->createMockResponse(204, []);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'DELETE',
                $this->stringContains('/api/profiles/42'),
                $this->anything()
            )
            ->willReturn($response);

        $this->client->deleteProfile(42);
    }

    #[TestDox('getProfile returns Profile DTO')]
    public function testGetProfile(): void
    {
        $response = $this->createMockResponse(200, [
            'id' => 15,
            'identifier' => 'https://twitter.com/criticalmassHH',
            'network' => ['id' => 108, 'identifier' => 'twitter'],
            'autoFetch' => true,
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $profile = $this->client->getProfile(15);

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals(15, $profile->getId());
    }

    #[TestDox('getProfile returns null on 404')]
    public function testGetProfileReturnsNullOn404(): void
    {
        $this->httpClient->method('request')
            ->willThrowException(new \RuntimeException('Not found'));

        $profile = $this->client->getProfile(999);

        $this->assertNull($profile);
    }

    #[TestDox('getTimelineItems returns array of FeedItem DTOs')]
    public function testGetTimelineItems(): void
    {
        $response = $this->createMockResponse(200, [
            'member' => [
                [
                    'id' => 1,
                    'uniqueIdentifier' => 'post-1',
                    'text' => 'Test post',
                    'dateTime' => '2026-03-15T18:00:00+01:00',
                    'createdAt' => '2026-03-15T19:00:00+01:00',
                    'profile' => ['id' => 7],
                ],
            ],
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $items = $this->client->getTimelineItems(limit: 10);

        $this->assertCount(1, $items);
        $this->assertInstanceOf(FeedItem::class, $items[0]);
        $this->assertEquals('Test post', $items[0]->getText());
    }

    #[TestDox('getTimelineItems passes date filters as query parameters')]
    public function testGetTimelineItemsWithDateFilters(): void
    {
        $response = $this->createMockResponse(200, ['member' => []]);

        $since = new \DateTimeImmutable('2026-03-01T00:00:00+01:00');
        $until = new \DateTimeImmutable('2026-03-15T23:59:59+01:00');

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->callback(function (string $url) use ($since, $until): bool {
                    return str_contains($url, 'since=' . urlencode($since->format(\DateTimeInterface::ATOM)))
                        && str_contains($url, 'until=' . urlencode($until->format(\DateTimeInterface::ATOM)));
                }),
                $this->anything()
            )
            ->willReturn($response);

        $this->client->getTimelineItems(since: $since, until: $until);
    }

    #[TestDox('throws RuntimeException on HTTP error')]
    public function testThrowsOnHttpError(): void
    {
        $response = $this->createMockResponse(500, []);

        $this->httpClient->method('request')->willReturn($response);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Feeds API returned status 500');

        $this->client->getNetworks();
    }

    #[TestDox('createProfile sends POST with correct body')]
    public function testCreateProfile(): void
    {
        // First call: getNetworks (to resolve IRI)
        $networksResponse = $this->createMockResponse(200, [
            'member' => [
                [
                    'id' => 108,
                    'identifier' => 'twitter',
                    'name' => 'Twitter',
                    'icon' => 'fab fa-twitter',
                    'backgroundColor' => 'rgb(29, 161, 242)',
                    'textColor' => 'white',
                    'profileUrlPattern' => '#.+#',
                ],
            ],
        ]);

        // Second call: createProfile
        $profileResponse = $this->createMockResponse(201, [
            'id' => 42,
            'identifier' => 'https://twitter.com/cm_hh',
            'network' => ['id' => 108, 'identifier' => 'twitter'],
            'autoFetch' => true,
        ]);

        $this->httpClient->method('request')
            ->willReturnOnConsecutiveCalls($networksResponse, $profileResponse);

        $profile = $this->client->createProfile('https://twitter.com/cm_hh', 'twitter');

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals(42, $profile->getId());
    }

    #[TestDox('createProfile throws when network not found')]
    public function testCreateProfileThrowsForUnknownNetwork(): void
    {
        $networksResponse = $this->createMockResponse(200, ['member' => []]);
        $this->httpClient->method('request')->willReturn($networksResponse);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Network "unknown_network" not found');

        $this->client->createProfile('https://example.com', 'unknown_network');
    }
}
