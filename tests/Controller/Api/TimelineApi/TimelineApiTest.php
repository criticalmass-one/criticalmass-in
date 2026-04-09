<?php declare(strict_types=1);

namespace Tests\Controller\Api\TimelineApi;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class TimelineApiTest extends AbstractApiControllerTestCase
{
    #[TestDox('Timeline API returns valid JSON structure with items and hasMore')]
    public function testValidJsonStructure(): void
    {
        $this->client->request('GET', '/api/timeline');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('hasMore', $response);
    }

    #[TestDox('Timeline API returns correct content type')]
    public function testContentType(): void
    {
        $this->client->request('GET', '/api/timeline');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    #[TestDox('Timeline API items contain JSON objects with type and dateTime')]
    public function testItemsContainJsonObjects(): void
    {
        $this->client->request('GET', '/api/timeline');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response['items']);

        foreach ($response['items'] as $item) {
            $this->assertIsArray($item);
            $this->assertArrayHasKey('type', $item);
            $this->assertArrayHasKey('dateTime', $item);
            $this->assertArrayHasKey('timeAgo', $item);
            $this->assertIsString($item['type']);
            $this->assertIsString($item['dateTime']);
            $this->assertIsString($item['timeAgo']);
        }
    }

    #[TestDox('Timeline API items have valid types')]
    public function testItemsHaveValidTypes(): void
    {
        $validTypes = [
            'cityCreated',
            'cityEdit',
            'rideComment',
            'rideEdit',
            'ridePhoto',
            'photoComment',
            'rideParticipationEstimate',
            'rideTrack',
            'thread',
            'threadPost',
            'socialNetworkFeedItem',
        ];

        $this->client->request('GET', '/api/timeline');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response['items'] as $item) {
            $this->assertContains($item['type'], $validTypes, sprintf('Invalid timeline item type: %s', $item['type']));
        }
    }

    #[TestDox('Timeline API hasMore is a boolean')]
    public function testHasMoreIsBoolean(): void
    {
        $this->client->request('GET', '/api/timeline');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsBool($response['hasMore']);
    }

    #[TestDox('Timeline API respects limit parameter')]
    public function testLimitParameter(): void
    {
        $this->client->request('GET', '/api/timeline?limit=2');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertLessThanOrEqual(2, count($response['items']));
    }

    #[TestDox('Timeline API respects offset parameter')]
    public function testOffsetParameter(): void
    {
        $this->client->request('GET', '/api/timeline?limit=100&offset=0');
        $allResponse = $this->getJsonResponse();

        $this->client->request('GET', '/api/timeline?limit=100&offset=2');
        $offsetResponse = $this->getJsonResponse();

        if (count($allResponse['items']) > 2) {
            $this->assertEquals($allResponse['items'][2], $offsetResponse['items'][0]);
        }
    }

    #[TestDox('Timeline API returns hasMore=false when all items are fetched')]
    public function testHasMoreFalseWhenAllFetched(): void
    {
        $this->client->request('GET', '/api/timeline?limit=10000&offset=0');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertFalse($response['hasMore']);
    }

    #[TestDox('Timeline API uses default limit when not specified')]
    public function testDefaultLimit(): void
    {
        $this->client->request('GET', '/api/timeline');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertLessThanOrEqual(10, count($response['items']));
    }
}
