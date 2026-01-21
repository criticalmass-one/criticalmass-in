<?php declare(strict_types=1);

namespace Tests\Controller\Api\TrackApi;

use App\Entity\Ride;
use App\Entity\Track;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class TrackApiTest extends AbstractApiControllerTestCase
{
    public function testListTracks(): void
    {
        $this->client->request('GET', '/api/track');

        $this->assertResponseIsSuccessful();

        $tracks = $this->deserializeEntityList($this->client->getResponse()->getContent(), Track::class);

        $this->assertNotEmpty($tracks);
        $this->assertContainsOnlyInstancesOf(Track::class, $tracks);
    }

    public function testListTracksWithSize(): void
    {
        $this->client->request('GET', '/api/track', ['size' => 2]);

        $this->assertResponseIsSuccessful();

        $tracks = $this->deserializeEntityList($this->client->getResponse()->getContent(), Track::class);

        $this->assertLessThanOrEqual(2, count($tracks));
    }

    public function testListTracksForRide(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides, 'No rides found in database');

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $tracks = $this->entityManager->getRepository(Track::class)->findByRide($ride);
            if (count($tracks) > 0) {
                $dateString = $ride->getDateTime()->format('Y-m-d');
                $citySlug = $ride->getCity()->getMainSlugString();

                $this->client->request('GET', sprintf('/api/%s/%s/listTracks', $citySlug, $dateString));

                $this->assertResponseIsSuccessful();

                $responseTracks = $this->deserializeEntityList($this->client->getResponse()->getContent(), Track::class);

                $this->assertNotEmpty($responseTracks);
                return;
            }
        }

        $this->markTestSkipped('No rides with tracks found');
    }

    public function testViewTrack(): void
    {
        $tracks = $this->entityManager->getRepository(Track::class)->findAll();
        $this->assertNotEmpty($tracks, 'No tracks found in database');

        /** @var Track $track */
        $track = $tracks[0];

        $this->client->request('GET', sprintf('/api/track/%d', $track->getId()));

        $this->assertResponseIsSuccessful();

        /** @var Track $responseTrack */
        $responseTrack = $this->deserializeEntity($this->client->getResponse()->getContent(), Track::class);

        $this->assertEquals($track->getId(), $responseTrack->getId());
        $this->assertEquals($track->getDistance(), $responseTrack->getDistance());
    }

    public function testViewUnknownTrackReturns404(): void
    {
        $this->client->request('GET', '/api/track/999999');

        $this->assertResponseStatusCode(404);
    }

    public function testTrackHasExpectedProperties(): void
    {
        $tracks = $this->entityManager->getRepository(Track::class)->findAll();
        $this->assertNotEmpty($tracks, 'No tracks found in database');

        /** @var Track $track */
        $track = $tracks[0];

        $this->client->request('GET', sprintf('/api/track/%d', $track->getId()));

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('distance', $response);
        $this->assertArrayHasKey('points', $response);
        $this->assertArrayHasKey('startDateTime', $response);
        $this->assertArrayHasKey('endDateTime', $response);
    }
}
