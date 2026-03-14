<?php declare(strict_types=1);

namespace Tests\Controller\Api\SocialNetworkFeedItemApi;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('SocialNetworkFeedItem API Tests')]
class SocialNetworkFeedItemApiTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems returns paginated response')]
    public function testFeedItemListReturnsPaginatedResponse(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertIsArray($response['data']);
        $this->assertArrayHasKey('page', $response['meta']);
        $this->assertArrayHasKey('size', $response['meta']);
        $this->assertArrayHasKey('totalItems', $response['meta']);
        $this->assertArrayHasKey('totalPages', $response['meta']);
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems returns default pagination meta')]
    public function testFeedItemListDefaultPagination(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertEquals(0, $response['meta']['page']);
        $this->assertEquals(100, $response['meta']['size']);
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems respects page and size parameters')]
    public function testFeedItemListCustomPagination(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems?page=0&size=2');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertEquals(0, $response['meta']['page']);
        $this->assertEquals(2, $response['meta']['size']);
        $this->assertLessThanOrEqual(2, count($response['data']));
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems returns feed items matching SOCIAL_NETWORK_FEED_ITEM_SCHEMA')]
    public function testFeedItemListResponseSchema(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (empty($response['data'])) {
            $this->markTestSkipped('No social network feed items found');
        }

        foreach ($response['data'] as $index => $feedItem) {
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
        $this->assertArrayHasKey('data', $response);
        $this->assertIsArray($response['data']);
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-feeditems supports networkIdentifier filter')]
    public function testFeedItemListWithNetworkIdentifierFilter(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems?networkIdentifier=twitter');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertArrayHasKey('data', $response);
        $this->assertIsArray($response['data']);
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
            'unique_identifier' => 'test-' . uniqid(),
            'text' => 'Test feed item content',
            'date_time' => time(),
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

            $feedItem = $this->entityManager->getRepository(SocialNetworkFeedItem::class)->find($response['id']);

            if ($feedItem) {
                $this->entityManager->remove($feedItem);
                $this->entityManager->flush();
            }
        }
    }

    #[TestDox('PUT /api/{citySlug}/socialnetwork-feeditems returns 400 without social_network_profile_id')]
    public function testCreateFeedItemWithoutProfileReturns400(): void
    {
        $newFeedItemData = [
            'unique_identifier' => 'test-' . uniqid(),
            'text' => 'Test feed item without profile',
            'date_time' => time(),
            'hidden' => false,
            'deleted' => false,
        ];

        $this->client->request(
            'PUT',
            '/api/hamburg/socialnetwork-feeditems',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newFeedItemData)
        );

        $this->assertResponseStatusCode(400);
    }

    #[TestDox('PUT /api/{citySlug}/socialnetwork-feeditems returns 400 for invalid social_network_profile_id')]
    public function testCreateFeedItemWithInvalidProfileReturns400(): void
    {
        $newFeedItemData = [
            'social_network_profile_id' => 999999,
            'unique_identifier' => 'test-' . uniqid(),
            'text' => 'Test feed item with invalid profile',
            'date_time' => time(),
            'hidden' => false,
            'deleted' => false,
        ];

        $this->client->request(
            'PUT',
            '/api/hamburg/socialnetwork-feeditems',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newFeedItemData)
        );

        $this->assertResponseStatusCode(400);
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
            'unique_identifier' => $existingFeedItem->getUniqueIdentifier(),
            'text' => 'Duplicate content',
            'date_time' => time(),
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

        if (empty($response['data'])) {
            $this->markTestSkipped('No feed items found');
        }

        $feedItem = $response['data'][0];
        $this->assertArrayHasKey('date_time', $feedItem);
        $this->assertIsInt($feedItem['date_time'], 'dateTime should be a Unix timestamp');
    }

    #[TestDox('Feed item hidden and deleted are booleans')]
    public function testFeedItemBooleanFields(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (empty($response['data'])) {
            $this->markTestSkipped('No feed items found');
        }

        foreach ($response['data'] as $feedItem) {
            $this->assertIsBool($feedItem['hidden']);
            $this->assertIsBool($feedItem['deleted']);
        }
    }

    #[TestDox('Pagination totalPages is calculated correctly')]
    public function testPaginationTotalPagesCalculation(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-feeditems?size=1');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $totalItems = $response['meta']['totalItems'];
        $totalPages = $response['meta']['totalPages'];

        if ($totalItems === 0) {
            $this->markTestSkipped('No feed items found');
        }

        $this->assertEquals($totalItems, $totalPages, 'With size=1, totalPages should equal totalItems');
        $this->assertCount(1, $response['data']);
    }

    #[TestDox('GET /api/socialnetwork-feeditems returns paginated response')]
    public function testGlobalFeedItemListReturnsPaginatedResponse(): void
    {
        $this->client->request('GET', '/api/socialnetwork-feeditems');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertIsArray($response['data']);
        $this->assertArrayHasKey('page', $response['meta']);
        $this->assertArrayHasKey('size', $response['meta']);
        $this->assertArrayHasKey('totalItems', $response['meta']);
        $this->assertArrayHasKey('totalPages', $response['meta']);
    }

    #[TestDox('GET /api/socialnetwork-feeditems supports networkIdentifier filter')]
    public function testGlobalFeedItemListWithNetworkIdentifierFilter(): void
    {
        $this->client->request('GET', '/api/socialnetwork-feeditems?networkIdentifier=twitter');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertArrayHasKey('data', $response);
        $this->assertIsArray($response['data']);
    }

    #[TestDox('GET /api/socialnetwork-feeditems supports since filter')]
    public function testGlobalFeedItemListWithSinceFilter(): void
    {
        $this->client->request('GET', '/api/socialnetwork-feeditems?since=' . strtotime('-1 year'));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertArrayHasKey('data', $response);
        $this->assertIsArray($response['data']);
    }

    #[TestDox('GET /api/socialnetwork-feeditems supports custom pagination')]
    public function testGlobalFeedItemListCustomPagination(): void
    {
        $this->client->request('GET', '/api/socialnetwork-feeditems?page=0&size=2');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertEquals(0, $response['meta']['page']);
        $this->assertEquals(2, $response['meta']['size']);
        $this->assertLessThanOrEqual(2, count($response['data']));
    }
}
