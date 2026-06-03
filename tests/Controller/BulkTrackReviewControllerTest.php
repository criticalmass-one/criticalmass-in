<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Criticalmass\Geo\Coord\Coord;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use League\Flysystem\FilesystemOperator;

class BulkTrackReviewControllerTest extends AbstractControllerTestCase
{
    private const REVIEW_URL = '/trackupload/review';

    public function testReviewPageAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', self::REVIEW_URL);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testReviewPageRedirectsAnonymous(): void
    {
        $client = static::createClient();

        $client->request('GET', self::REVIEW_URL);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testConfirmMatchedCandidateCreatesTrackAndRemovesCandidate(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride);

        $candidate = $this->createStoredCandidate('testuser@criticalmass.in', $ride);
        $candidateId = $candidate->getId();

        $trackCountBefore = $this->countTracksForRide($ride->getId());

        $token = $this->reviewToken($client);
        $client->request('POST', sprintf('%s/%d/confirm', self::REVIEW_URL, $candidateId), ['_token' => $token]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $em = static::getContainer()->get('doctrine')->getManager();
        $this->assertNull($em->getRepository(TrackImportCandidate::class)->find($candidateId), 'Confirmed candidate should be removed');
        $this->assertEquals($trackCountBefore + 1, $this->countTracksForRide($ride->getId()), 'A track should be created from the candidate');
    }

    public function testRejectRemovesCandidate(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $candidate = $this->createStoredCandidate('testuser@criticalmass.in', null);
        $candidateId = $candidate->getId();

        $token = $this->reviewToken($client);
        $client->request('POST', sprintf('%s/%d/reject', self::REVIEW_URL, $candidateId), ['_token' => $token]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $em = static::getContainer()->get('doctrine')->getManager();
        $this->assertNull($em->getRepository(TrackImportCandidate::class)->find($candidateId), 'Rejected candidate should be removed');
    }

    public function testRejectRejectsInvalidCsrfToken(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $candidate = $this->createStoredCandidate('testuser@criticalmass.in', null);

        $client->request('POST', sprintf('%s/%d/reject', self::REVIEW_URL, $candidate->getId()), ['_token' => 'invalid']);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    private function reviewToken($client): string
    {
        $crawler = $client->request('GET', self::REVIEW_URL);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $token = $crawler->filter('input[name="_token"]')->first();
        $this->assertGreaterThan(0, $token->count(), 'Review page should expose a CSRF token');

        return $token->attr('value');
    }

    private function createStoredCandidate(string $email, ?Ride $ride): TrackImportCandidate
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        $hash = sha1(uniqid('candidate', true));
        $storagePath = sprintf('candidates/%s.gpx', $hash);

        /** @var FilesystemOperator $trackFilesystem */
        $trackFilesystem = static::getContainer()->get('oneup_flysystem.flysystem_track_track_filesystem');
        $trackFilesystem->write($storagePath, $this->minimalGpx());

        $candidate = new TrackImportCandidate();
        $candidate
            ->setUser($user)
            ->setRide($ride)
            ->setSource(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->setType('Ride')
            ->setName('review-test.gpx')
            ->setOriginalName('review-test.gpx')
            ->setFileHash($hash)
            ->setTrackFilename($storagePath)
            ->setStartDateTime(new \DateTime('2024-06-01T19:00:00Z'))
            ->setStartCoord(new Coord(53.55, 9.99))
            ->setEndCoord(new Coord(53.56, 9.98))
            ->setElapsedTime(3600)
            ->setDistance(5000.0)
            ->setPolyline('}_~iHnkxh@');

        $em->persist($candidate);
        $em->flush();

        return $candidate;
    }

    private function minimalGpx(): string
    {
        $gpx = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $gpx .= '<gpx version="1.1" creator="test"><trk><name>Review</name><trkseg>' . "\n";

        $dateTime = new \DateTime('2024-06-01T19:00:00Z');

        for ($i = 0; $i < 30; $i++) {
            $angle = 2 * M_PI * $i / 30;
            $gpx .= sprintf(
                '<trkpt lat="%.6f" lon="%.6f"><time>%s</time></trkpt>' . "\n",
                53.55 + sin($angle) * 0.01,
                9.99 + cos($angle) * 0.01,
                $dateTime->format('Y-m-d\TH:i:s\Z'),
            );
            $dateTime->modify('+2 minutes');
        }

        $gpx .= '</trkseg></trk></gpx>';

        return $gpx;
    }

    private function getPastRideForCity(string $citySlug): ?Ride
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :citySlug')
            ->andWhere('r.dateTime < :now')
            ->setParameter('citySlug', $citySlug)
            ->setParameter('now', new \DateTime())
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function countTracksForRide(int $rideId): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return (int) $em->getRepository(Track::class)->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.ride = :rideId')
            ->setParameter('rideId', $rideId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
