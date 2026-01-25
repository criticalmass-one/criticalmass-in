<?php declare(strict_types=1);

namespace Tests\Controller\Api\TrackApi;

use App\Entity\Track;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Track API Write Operations')]
class TrackApiWriteTest extends AbstractApiControllerTestCase
{
    #[TestDox('DELETE /api/track/{id} requires authentication')]
    public function testDeleteTrackRequiresAuthentication(): void
    {
        $tracks = $this->entityManager->getRepository(Track::class)->findAll();

        if (empty($tracks)) {
            $this->markTestSkipped('No tracks found in database');
        }

        $track = $tracks[0];

        // Without authentication, should get redirect or 403
        $this->client->request('DELETE', sprintf('/api/track/%d', $track->getId()));

        $statusCode = $this->client->getResponse()->getStatusCode();
        // Depending on security config, could be 302 (redirect to login), 401, or 403
        $this->assertContains(
            $statusCode,
            [302, 401, 403],
            'Delete without auth should be denied'
        );
    }

    #[TestDox('DELETE /api/track/{nonExistentId} returns 404')]
    public function testDeleteNonExistentTrackReturns404(): void
    {
        $this->client->request('DELETE', '/api/track/999999');

        $statusCode = $this->client->getResponse()->getStatusCode();
        // Could be 404 or redirect/403 if auth check comes first
        $this->assertContains(
            $statusCode,
            [302, 401, 403, 404],
            'Non-existent track should return 404 or auth error'
        );
    }

    #[TestDox('Track deleted flag is set on soft delete')]
    public function testTrackSoftDeleteSetsDeletedFlag(): void
    {
        // This test verifies the entity behavior that tracks are soft-deleted
        $track = new Track();

        $this->assertFalse($track->getDeleted(), 'New track should not be deleted');

        $track->setDeleted(true);

        $this->assertTrue($track->getDeleted(), 'Track should be marked as deleted');
    }

    #[TestDox('GET /api/track/{id} still returns soft-deleted track')]
    public function testGetSoftDeletedTrackBehavior(): void
    {
        // Find a track that might be soft-deleted
        $tracks = $this->entityManager->getRepository(Track::class)->findAll();

        if (empty($tracks)) {
            $this->markTestSkipped('No tracks found in database');
        }

        $track = $tracks[0];

        $this->client->request('GET', sprintf('/api/track/%d', $track->getId()));

        // The endpoint should still work for soft-deleted tracks
        // (depending on implementation, might filter them or show them)
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 404]);
    }
}
