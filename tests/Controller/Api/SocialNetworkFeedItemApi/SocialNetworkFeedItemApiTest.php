<?php declare(strict_types=1);

namespace Tests\Controller\Api\SocialNetworkFeedItemApi;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use App\Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use App\Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('SocialNetworkFeedItem API Tests')]
class SocialNetworkFeedItemApiTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems returns array')]
    public function testFeedItemListReturnsArray(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems returns feed items matching SOCIAL_NETWORK_FEED_ITEM_SCHEMA')]
    public function testFeedItemListResponseSchema(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (empty($response)) {
            $this->markTestSkipped('No social network feed items found');
        }

        foreach ($response as $index => $feedItem) {
            $this->assertIsArray($feedItem, "Feed item at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::SOCIAL_NETWORK_FEED_ITEM_SCHEMA,
                $feedItem,
                "feedItems[{$index}]"
            );
        }
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems supports uniqueIdentifier filter')]
    public function testFeedItemListWithUniqueIdentifierFilter(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems?uniqueIdentifier=test123');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems supports networkIdentifier filter')]
    public function testFeedItemListWithNetworkIdentifierFilter(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems?networkIdentifier=twitter');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('GET /api/{invalidCity}/socialnetwork-feeditems returns 404')]
    public function testFeedItemListForInvalidCityReturns404(): void
    {
        $this->client->request('GET', '/api/nonexistent-city/socialnetwork-feeditems');
        $this->assertResponseStatusCode(404);
    }

    #[TestDox('POST /api/{citySlug}/socialnetwork-feeditems/{feedItemId} updates feed item')]
    public function testUpdateFeedItem(): void
    {
        $feedItems = $this->entityManager->getRepository(SocialNetworkFeedItem::class)->findAll();

        if (empty($feedItems)) {
            $this->markTestSkipped('No feed items found in database');
        }

        $feedItem = $feedItems[0];
        $city = $feedItem->getSocialNetworkProfile()->getCity();
        $citySlug = $city->getMainSlugString();

        $updateData = [
            'hidden' => true,
        ];

        $this->client->request(
            'POST',
            sprintf('/api/%s/socialnetwork-feeditems/%d', $citySlug, $feedItem->getId()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('PUT /api/{citySlug}/socialnetwork-feeditems creates new feed item')]
    public function testCreateFeedItem(): void
    {
        $profiles = $this->entityManager->getRepository(SocialNetworkProfile::class)->findAll();

        if (empty($profiles)) {
            $this->markTestSkipped('No social network profiles found in database');
        }

        $profile = $profiles[0];
        $city = $profile->getCity();
        $citySlug = $city->getMainSlugString();

        $newFeedItemData = [
            'social_network_profile_id' => $profile->getId(),
            'uniqueidentifier' => 'test-' . uniqid(),
            'text' => 'Test feed item content',
            'datetime' => time(),
            'hidden' => false,
            'deleted' => false,
        ];

        $this->client->request(
            'PUT',
            sprintf('/api/%s/socialnetwork-feeditems', $citySlug),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newFeedItemData)
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [201, 409], 'Should return 201 (created) or 409 (conflict)');

        if ($statusCode === 201) {
            $response = $this->getJsonResponse();
            $this->assertIsArray($response);
            $this->assertArrayHasKey('id', $response);
        }
    }

    #[TestDox('PUT /api/{citySlug}/socialnetwork-feeditems returns 409 for duplicate')]
    public function testCreateDuplicateFeedItemReturns409(): void
    {
        $feedItems = $this->entityManager->getRepository(SocialNetworkFeedItem::class)->findAll();

        if (empty($feedItems)) {
            $this->markTestSkipped('No feed items found in database');
        }

        $existingFeedItem = $feedItems[0];
        $city = $existingFeedItem->getSocialNetworkProfile()->getCity();
        $citySlug = $city->getMainSlugString();

        $duplicateData = [
            'social_network_profile_id' => $existingFeedItem->getSocialNetworkProfile()->getId(),
            'uniqueidentifier' => $existingFeedItem->getUniqueIdentifier(),
            'text' => 'Duplicate content',
            'datetime' => time(),
            'hidden' => false,
            'deleted' => false,
        ];

        $this->client->request(
            'PUT',
            sprintf('/api/%s/socialnetwork-feeditems', $citySlug),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($duplicateData)
        );

        $this->assertResponseStatusCode(409);
    }

    #[TestDox('Feed item dateTime is a Unix timestamp')]
    public function testFeedItemDateTimeIsUnixTimestamp(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (empty($response)) {
            $this->markTestSkipped('No feed items found');
        }

        $feedItem = $response[0];
        $this->assertArrayHasKey('datetime', $feedItem);
        $this->assertIsInt($feedItem['datetime'], 'dateTime should be a Unix timestamp');
    }

    #[TestDox('Feed item hidden and deleted are booleans')]
    public function testFeedItemBooleanFields(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (empty($response)) {
            $this->markTestSkipped('No feed items found');
        }

        foreach ($response as $feedItem) {
            $this->assertIsBool($feedItem['hidden']);
            $this->assertIsBool($feedItem['deleted']);
        }
    }
}
