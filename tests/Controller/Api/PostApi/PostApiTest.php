<?php declare(strict_types=1);

namespace Tests\Controller\Api\PostApi;

use App\Entity\Post;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class PostApiTest extends AbstractApiControllerTestCase
{
    public function testListPosts(): void
    {
        $this->client->request('GET', '/api/post');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
    }

    public function testListPostsReturnsOnlyEnabledPosts(): void
    {
        $this->client->request('GET', '/api/post', ['size' => 100]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        foreach ($response as $post) {
            $postEntity = $this->entityManager->getRepository(Post::class)->find($post['id']);
            $this->assertTrue($postEntity->getEnabled(), 'Only enabled posts should be returned');
        }
    }

    public function testListPostsWithSize(): void
    {
        $this->client->request('GET', '/api/post', ['size' => 2]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertLessThanOrEqual(2, count($response));
    }

    public function testListPostsForCity(): void
    {
        $this->client->request('GET', '/api/post', ['citySlug' => 'hamburg']);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        foreach ($response as $post) {
            $this->assertEquals('hamburg', $post['city_slug']);
        }
    }

    public function testListPostsOrderByDateTime(): void
    {
        $this->client->request('GET', '/api/post', [
            'orderBy' => 'dateTime',
            'orderDirection' => 'desc',
            'size' => 10,
        ]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        if (count($response) > 1) {
            $previousTimestamp = null;
            foreach ($response as $post) {
                $currentTimestamp = $post['date_time'];
                if ($previousTimestamp !== null) {
                    $this->assertGreaterThanOrEqual($currentTimestamp, $previousTimestamp, 'Posts should be ordered by dateTime descending');
                }
                $previousTimestamp = $currentTimestamp;
            }
        }
    }

    public function testListPostsOrderByDateTimeAscending(): void
    {
        $this->client->request('GET', '/api/post', [
            'orderBy' => 'dateTime',
            'orderDirection' => 'asc',
            'size' => 10,
        ]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        if (count($response) > 1) {
            $previousTimestamp = null;
            foreach ($response as $post) {
                $currentTimestamp = $post['date_time'];
                if ($previousTimestamp !== null) {
                    $this->assertLessThanOrEqual($currentTimestamp, $previousTimestamp, 'Posts should be ordered by dateTime ascending');
                }
                $previousTimestamp = $currentTimestamp;
            }
        }
    }

    public function testPostHasExpectedProperties(): void
    {
        $this->client->request('GET', '/api/post', ['size' => 1]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        if (!empty($response)) {
            $post = $response[0];

            $this->assertArrayHasKey('id', $post);
            $this->assertNotNull($post['id']);
            $this->assertArrayHasKey('message', $post);
            $this->assertArrayHasKey('date_time', $post);
        }
    }

    public function testPostContainsUsernameButNoSensitiveUserData(): void
    {
        $this->client->request('GET', '/api/post', ['size' => 1]);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        if (!empty($response)) {
            $post = $response[0];

            $this->assertArrayHasKey('user', $post);
            $this->assertIsArray($post['user']);
            $this->assertArrayHasKey('username', $post['user']);

            $this->assertArrayNotHasKey('email', $post['user']);
            $this->assertArrayNotHasKey('password', $post['user']);
            $this->assertArrayNotHasKey('roles', $post['user']);
            $this->assertArrayNotHasKey('facebook_id', $post['user']);
            $this->assertArrayNotHasKey('strava_id', $post['user']);
            $this->assertArrayNotHasKey('facebook_access_token', $post['user']);
            $this->assertArrayNotHasKey('strava_access_token', $post['user']);
        }
    }
}
