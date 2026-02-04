<?php declare(strict_types=1);

namespace Tests\Controller\Api\PostApi;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;
use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;

#[TestDox('Post API Schema Validation')]
class PostApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/post returns array of posts matching POST_SCHEMA')]
    public function testPostListResponseSchema(): void
    {
        $this->client->request('GET', '/api/post');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        if (!empty($response)) {
            foreach ($response as $index => $post) {
                $this->assertIsArray($post, "Post at index {$index} should be an array");
                JsonStructureValidator::assertMatchesSchema(
                    ApiSchemaDefinitions::POST_SCHEMA,
                    $post,
                    "posts[{$index}]"
                );
            }
        }
    }

    #[TestDox('Post dateTime is a Unix timestamp')]
    public function testPostDateTimeIsUnixTimestamp(): void
    {
        $this->client->request('GET', '/api/post?size=1');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (!empty($response)) {
            $post = $response[0];
            $this->assertArrayHasKey('date_time', $post);
            $this->assertIsInt($post['date_time'], 'dateTime should be a Unix timestamp');
            $this->assertGreaterThan(0, $post['date_time'], 'dateTime should be a positive timestamp');
        }
    }

    #[TestDox('Post coordinates are valid when present')]
    public function testPostCoordinatesAreValidWhenPresent(): void
    {
        $this->client->request('GET', '/api/post?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $post) {
            if (isset($post['latitude']) && $post['latitude'] !== null) {
                $this->assertGreaterThanOrEqual(-90, $post['latitude']);
                $this->assertLessThanOrEqual(90, $post['latitude']);
            }
            if (isset($post['longitude']) && $post['longitude'] !== null) {
                $this->assertGreaterThanOrEqual(-180, $post['longitude']);
                $this->assertLessThanOrEqual(180, $post['longitude']);
            }
        }
    }

    #[TestDox('Post user contains only public fields')]
    public function testPostUserContainsOnlyPublicFields(): void
    {
        $this->client->request('GET', '/api/post?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $post) {
            if (isset($post['user']) && is_array($post['user'])) {
                $this->assertArrayHasKey('id', $post['user']);
                $this->assertArrayHasKey('username', $post['user']);

                $sensitiveFields = ['email', 'password', 'roles', 'facebook_id', 'strava_id', 'facebook_access_token', 'strava_access_token'];
                foreach ($sensitiveFields as $field) {
                    $this->assertArrayNotHasKey($field, $post['user'], "User should not contain sensitive field: {$field}");
                }
            }
        }
    }

    #[TestDox('GET /api/post supports size parameter')]
    public function testPostListSizeParameter(): void
    {
        $this->client->request('GET', '/api/post?size=5');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertLessThanOrEqual(5, count($response));
    }

    #[TestDox('Post message is a string when present')]
    public function testPostMessageIsStringWhenPresent(): void
    {
        $this->client->request('GET', '/api/post?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $post) {
            if (isset($post['message']) && $post['message'] !== null) {
                $this->assertIsString($post['message']);
            }
        }
    }

    #[TestDox('Post reference IDs are integers when present')]
    public function testPostReferenceIdsAreIntegersWhenPresent(): void
    {
        $this->client->request('GET', '/api/post?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $post) {
            if (isset($post['ride_id']) && $post['ride_id'] !== null) {
                $this->assertIsInt($post['ride_id']);
            }
            if (isset($post['city_id']) && $post['city_id'] !== null) {
                $this->assertIsInt($post['city_id']);
            }
            if (isset($post['photo_id']) && $post['photo_id'] !== null) {
                $this->assertIsInt($post['photo_id']);
            }
            if (isset($post['thread_id']) && $post['thread_id'] !== null) {
                $this->assertIsInt($post['thread_id']);
            }
        }
    }
}
