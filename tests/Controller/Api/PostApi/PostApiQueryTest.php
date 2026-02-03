<?php declare(strict_types=1);

namespace Tests\Controller\Api\PostApi;

use App\Entity\City;
use App\Entity\Post;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Post API Query Tests')]
class PostApiQueryTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/post with citySlug returns only posts from that city')]
    public function testFilterByCitySlug(): void
    {
        $this->client->request('GET', '/api/post', ['citySlug' => 'hamburg', 'size' => 50]);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $post) {
            if ($post['city_slug'] !== null) {
                $this->assertEquals('hamburg', $post['city_slug'], 'All posts should be from Hamburg');
            }
        }
    }

    #[TestDox('GET /api/post with non-existent citySlug returns empty list')]
    public function testFilterByNonExistentCitySlug(): void
    {
        $this->client->request('GET', '/api/post', ['citySlug' => 'nonexistent-city-slug-xyz']);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    #[TestDox('GET /api/post with size=1 returns at most 1 post')]
    public function testSizeParameter(): void
    {
        $this->client->request('GET', '/api/post', ['size' => 1]);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertLessThanOrEqual(1, count($response));
    }

    #[TestDox('GET /api/post with orderBy=dateTime&orderDirection=desc returns posts in descending order')]
    public function testOrderByDateTimeDesc(): void
    {
        $this->client->request('GET', '/api/post', [
            'orderBy' => 'dateTime',
            'orderDirection' => 'desc',
            'size' => 20,
        ]);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

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

    #[TestDox('GET /api/post with orderBy=dateTime&orderDirection=asc returns posts in ascending order')]
    public function testOrderByDateTimeAsc(): void
    {
        $this->client->request('GET', '/api/post', [
            'orderBy' => 'dateTime',
            'orderDirection' => 'asc',
            'size' => 20,
        ]);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

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

    #[TestDox('GET /api/post with orderBy=id returns posts ordered by id')]
    public function testOrderById(): void
    {
        $this->client->request('GET', '/api/post', [
            'orderBy' => 'id',
            'orderDirection' => 'asc',
            'size' => 20,
        ]);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        if (count($response) > 1) {
            $previousId = null;
            foreach ($response as $post) {
                $currentId = $post['id'];
                if ($previousId !== null) {
                    $this->assertLessThanOrEqual($currentId, $previousId, 'Posts should be ordered by id ascending');
                }
                $previousId = $currentId;
            }
        }
    }

    #[TestDox('GET /api/post never returns disabled posts')]
    public function testDisabledPostsAreNotReturned(): void
    {
        $this->client->request('GET', '/api/post', ['size' => 100]);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $post) {
            $postEntity = $this->entityManager->getRepository(Post::class)->find($post['id']);
            $this->assertNotNull($postEntity);
            $this->assertTrue($postEntity->getEnabled(), 'Only enabled posts should be returned');
        }
    }

    #[TestDox('GET /api/post with multiple filters combines them correctly')]
    public function testMultipleFilters(): void
    {
        $this->client->request('GET', '/api/post', [
            'citySlug' => 'hamburg',
            'orderBy' => 'dateTime',
            'orderDirection' => 'desc',
            'size' => 5,
        ]);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertLessThanOrEqual(5, count($response));

        foreach ($response as $post) {
            if ($post['city_slug'] !== null) {
                $this->assertEquals('hamburg', $post['city_slug']);
            }
        }
    }
}
