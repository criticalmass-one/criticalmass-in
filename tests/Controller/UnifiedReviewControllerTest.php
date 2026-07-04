<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Criticalmass\Geo\Coord\Coord;
use App\Entity\Photo;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UnifiedReviewControllerTest extends AbstractControllerTestCase
{
    private const REVIEW_URL = '/upload/review';
    private const USER_EMAIL = 'testuser@criticalmass.in';
    private const MINIMAL_JPEG_BASE64 = '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAALCAABAAEBAREA/8QAFAABAAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AfwD/2Q==';

    public function testReviewPageAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        $client->request('GET', self::REVIEW_URL);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testReviewPageRedirectsAnonymous(): void
    {
        $client = static::createClient();

        $client->request('GET', self::REVIEW_URL);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testOldTrackReviewRedirectsToUnifiedReview(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        $client->request('GET', '/trackupload/review');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertStringEndsWith('/upload/review', (string) $client->getResponse()->headers->get('Location'));
    }

    public function testPhotoPreviewReturnsImageForOwner(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        $candidate = $this->createStagedCandidate($this->user(), new \DateTime('2019-03-15 18:00:00'), 'prev' . uniqid());

        $client->request('GET', sprintf('/upload/review/photo/%d/preview', $candidate->getId()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('image/jpeg', $client->getResponse()->headers->get('Content-Type'));
        $this->assertNotEmpty($client->getResponse()->getContent());
    }

    public function testRejectGalleryDeletesCandidatesAndFiles(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        $date = new \DateTime('2018-07-21 12:00:00');
        $a = $this->createStagedCandidate($this->user(), $date, 'rejA' . uniqid());
        $b = $this->createStagedCandidate($this->user(), $date, 'rejB' . uniqid());
        $pathA = (string) $a->getStagedFilename();

        $token = $this->reviewToken($client);
        $client->request('POST', sprintf('/upload/review/photos/%s/reject', $date->format('Y-m-d')), ['_token' => $token]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $em = $this->em();
        $this->assertNull($em->getRepository(PhotoImportCandidate::class)->find($a->getId()));
        $this->assertNull($em->getRepository(PhotoImportCandidate::class)->find($b->getId()));
        $this->assertFalse($this->candidateFilesystem()->fileExists($pathA), 'Staged file should be deleted on reject');
    }

    public function testConfirmGalleryImportsPhotosToRide(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        $ride = $this->anyRide();
        $date = $ride->getDateTime();

        $this->createStagedCandidate($this->user(), $date, 'confA' . uniqid());
        $this->createStagedCandidate($this->user(), $date, 'confB' . uniqid());

        $before = $this->countPhotosForRide($ride->getId());

        $token = $this->reviewToken($client);
        $client->request('POST', sprintf('/upload/review/photos/%s/confirm', $date->format('Y-m-d')), [
            '_token' => $token,
            'rideId' => $ride->getId(),
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertSame($before + 2, $this->countPhotosForRide($ride->getId()), 'Both photos should be imported to the ride');
        $this->assertSame(0, $this->countActiveCandidates($this->user(), $date), 'Candidates should be consumed');
    }

    public function testConfirmGalleryRejectsRideFromAnotherDate(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        [$rideA, $rideB] = $this->twoRidesOnDifferentDates();
        $date = $rideA->getDateTime();

        $this->createStagedCandidate($this->user(), $date, 'mismatchA' . uniqid());

        $token = $this->reviewToken($client);
        $client->request('POST', sprintf('/upload/review/photos/%s/confirm', $date->format('Y-m-d')), [
            '_token' => $token,
            'rideId' => $rideB->getId(),
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $this->countActiveCandidates($this->user(), $date), 'Gallery must not be imported with a ride from another day');
    }

    public function testReassignTrackToSameDateRide(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        $ride = $this->anyRide();
        $candidate = $this->createParkedTrack($this->user(), $ride->getDateTime());

        $token = $this->reviewToken($client);
        $client->request('POST', sprintf('/upload/review/track/%d/reassign', $candidate->getId()), [
            '_token' => $token,
            'rideId' => $ride->getId(),
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $this->em()->clear();
        $reloaded = $this->em()->getRepository(TrackImportCandidate::class)->find($candidate->getId());
        $this->assertNotNull($reloaded->getRide());
        $this->assertSame($ride->getId(), $reloaded->getRide()->getId());
    }

    public function testReassignTrackRejectsRideFromAnotherDate(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER_EMAIL);

        [$rideA, $rideB] = $this->twoRidesOnDifferentDates();
        $candidate = $this->createParkedTrack($this->user(), $rideA->getDateTime());

        $token = $this->reviewToken($client);
        $client->request('POST', sprintf('/upload/review/track/%d/reassign', $candidate->getId()), [
            '_token' => $token,
            'rideId' => $rideB->getId(),
        ]);

        $this->em()->clear();
        $reloaded = $this->em()->getRepository(TrackImportCandidate::class)->find($candidate->getId());
        $this->assertNull($reloaded->getRide(), 'A track must not be reassigned to a ride on another day');
    }

    private function reviewToken(KernelBrowser $client): string
    {
        $crawler = $client->request('GET', self::REVIEW_URL);

        $node = $crawler->filter('input[name="_token"]')->first();
        if ($node->count() > 0) {
            return (string) $node->attr('value');
        }

        // No forms rendered (nothing to review yet) — generate a valid token directly.
        return (string) static::getContainer()->get('security.csrf.token_manager')->getToken('unified_review');
    }

    private function createStagedCandidate(User $user, \DateTime $exifDate, string $hash): PhotoImportCandidate
    {
        $stagedFilename = $hash . '.jpg';
        $this->candidateFilesystem()->write($stagedFilename, base64_decode(self::MINIMAL_JPEG_BASE64));

        $candidate = (new PhotoImportCandidate())
            ->setUser($user)
            ->setFileHash($hash)
            ->setStagedFilename($stagedFilename)
            ->setOriginalName($hash . '.jpg')
            ->setMimeType('image/jpeg')
            ->setExifCreationDate($exifDate);

        $em = $this->em();
        $em->persist($candidate);
        $em->flush();

        return $candidate;
    }

    private function createParkedTrack(User $user, \DateTime $startDateTime): TrackImportCandidate
    {
        $candidate = (new TrackImportCandidate())
            ->setUser($user)
            ->setSource(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->setType('Ride')
            ->setName('reassign-test')
            ->setOriginalName('reassign-test.gpx')
            ->setFileHash('trk' . uniqid())
            ->setStartDateTime($startDateTime)
            ->setStartCoord(new Coord(53.55, 9.99))
            ->setEndCoord(new Coord(53.56, 9.98))
            ->setDistance(1000.0)
            ->setElapsedTime(600)
            ->setPolyline('}_~iHnkxh@');

        $em = $this->em();
        $em->persist($candidate);
        $em->flush();

        return $candidate;
    }

    private function countActiveCandidates(User $user, \DateTime $date): int
    {
        return count(array_filter(
            $this->em()->getRepository(PhotoImportCandidate::class)->findBy(['user' => $user, 'rejected' => false]),
            static fn (PhotoImportCandidate $c): bool => $c->getGalleryKey() === $date->format('Y-m-d'),
        ));
    }

    private function countPhotosForRide(int $rideId): int
    {
        return (int) $this->em()->getRepository(Photo::class)->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.ride = :rideId')
            ->setParameter('rideId', $rideId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function anyRide(): Ride
    {
        $ride = $this->em()->getRepository(Ride::class)->findOneBy([], ['id' => 'ASC']);
        self::assertInstanceOf(Ride::class, $ride, 'Fixtures must provide at least one ride');

        return $ride;
    }

    /**
     * @return array{0: Ride, 1: Ride}
     */
    private function twoRidesOnDifferentDates(): array
    {
        /** @var list<Ride> $rides */
        $rides = $this->em()->getRepository(Ride::class)->findBy([], ['dateTime' => 'ASC'], 200);

        $first = $rides[0];
        foreach ($rides as $ride) {
            if ($ride->getDateTime()->format('Y-m-d') !== $first->getDateTime()->format('Y-m-d')) {
                return [$first, $ride];
            }
        }

        self::fail('Fixtures must provide rides on at least two different dates');
    }

    private function user(): User
    {
        $user = $this->em()->getRepository(User::class)->findOneBy(['email' => self::USER_EMAIL]);
        self::assertInstanceOf(User::class, $user);

        return $user;
    }

    private function em(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    private function candidateFilesystem(): FilesystemOperator
    {
        return static::getContainer()->get('oneup_flysystem.flysystem_photo_candidate_filesystem');
    }
}
