<?php declare(strict_types=1);

namespace Tests\Controller\Api\PostApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Post;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests der schreibenden Post-Endpunkte (create/show/update/delete).
 * Transaktions-isoliert.
 */
class PostApiWriteTest extends AbstractApiControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    private function createCity(): City
    {
        $slug = 'post-api-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Poststadt');
        $city->setTitle('Critical Mass Poststadt');
        $city->setCreatedAt(new \DateTime());
        $this->entityManager->persist($city);

        $citySlug = new CitySlug();
        $citySlug->setSlug($slug);
        $citySlug->setCity($city);
        $this->entityManager->persist($citySlug);
        $city->setMainSlug($citySlug);
        $this->entityManager->flush();

        return $city;
    }

    private function addPost(City $city, string $message = 'Testbeitrag'): Post
    {
        $post = new Post();
        $post->setCity($city);
        $post->setMessage($message);
        $post->setDateTime(new \DateTime('2026-09-01 12:00:00'));
        $post->setEnabled(true);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    public function testCreatePost(): void
    {
        $city = $this->createCity();

        $this->client->request('PUT', '/api/' . $city->getMainSlugString() . '/post', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['message' => 'Hallo Welt']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $this->entityManager->getRepository(Post::class)->findBy(['city' => $city]));
    }

    public function testCreatePostRejectsBlankMessage(): void
    {
        $city = $this->createCity();

        $this->client->request('PUT', '/api/' . $city->getMainSlugString() . '/post', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['message' => '']));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testShowPost(): void
    {
        $city = $this->createCity();
        $post = $this->addPost($city, 'Sichtbar');

        $this->client->request('GET', '/api/post/' . $post->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Sichtbar', $data['message']);
    }

    public function testUpdatePost(): void
    {
        $city = $this->createCity();
        $post = $this->addPost($city, 'Alt');
        $postId = $post->getId();

        $this->client->request('POST', '/api/post/' . $postId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['message' => 'Neu']));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $updated = $this->entityManager->getRepository(Post::class)->find($postId);
        $this->assertSame('Neu', $updated?->getMessage());
    }

    public function testDeletePost(): void
    {
        $city = $this->createCity();
        $post = $this->addPost($city);
        $postId = $post->getId();

        $this->client->request('DELETE', '/api/post/' . $postId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(Post::class)->find($postId));
    }
}
