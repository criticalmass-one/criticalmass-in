<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\Track;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Form;

class TrackUploadControllerTest extends AbstractControllerTestCase
{
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

    private function buildRideUrl(Ride $ride): string
    {
        return sprintf('/%s/%s', $ride->getCity()->getMainSlugString(), $ride->getDateTime()->format('Y-m-d'));
    }

    private function uploadFileToForm(Form $form, string $filePath): void
    {
        /** @var FileFormField $fileField */
        $fileField = $form['form[trackFile][file]'];
        $fileField->upload($filePath);
    }

    private function generateGpxFile(float $startLat, float $startLon, string $startTime, int $pointCount = 60, float $radius = 0.01): string
    {
        $gpx = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $gpx .= '<gpx version="1.1" creator="test">' . "\n";
        $gpx .= '<trk><name>Test Track</name><trkseg>' . "\n";

        $dateTime = new \DateTime($startTime);

        for ($i = 0; $i < $pointCount; $i++) {
            $angle = 2 * M_PI * $i / $pointCount;
            $lat = $startLat + sin($angle) * $radius;
            $lon = $startLon + cos($angle) * $radius;

            $gpx .= sprintf(
                '<trkpt lat="%.6f" lon="%.6f"><time>%s</time></trkpt>' . "\n",
                $lat,
                $lon,
                $dateTime->format('Y-m-d\TH:i:s\Z')
            );

            $dateTime->modify('+2 minutes');
        }

        $gpx .= '</trkseg></trk></gpx>';

        $tmpFile = tempnam(sys_get_temp_dir(), 'gpx_test_');
        file_put_contents($tmpFile, $gpx);

        return $tmpFile;
    }

    public function testTrackUploadFormAccessible(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testTrackUploadRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testTrackUploadRedirectsAfterSubmission(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $gpxFile = $this->generateGpxFile(53.55, 9.99, '2024-06-01T19:00:00Z');

        try {
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');

            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile);

            $client->submit($form);

            $this->assertEquals(302, $client->getResponse()->getStatusCode());
        } finally {
            @unlink($gpxFile);
        }
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

    private function getNewestTrackForRide(int $rideId): ?Track
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(Track::class)->createQueryBuilder('t')
            ->where('t.ride = :rideId')
            ->setParameter('rideId', $rideId)
            ->orderBy('t.creationDateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function testTrackExistsInDatabaseAfterUpload(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('berlin');
        $this->assertNotNull($ride, 'Past Berlin ride fixture should exist');

        $trackCountBefore = $this->countTracksForRide($ride->getId());

        $gpxFile = $this->generateGpxFile(52.50, 13.42, '2024-06-01T19:00:00Z');

        try {
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');

            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile);

            $client->submit($form);

            $trackCountAfter = $this->countTracksForRide($ride->getId());
            $this->assertEquals($trackCountBefore + 1, $trackCountAfter, 'One new track should exist after upload');

            $uploadedTrack = $this->getNewestTrackForRide($ride->getId());

            $this->assertNotNull($uploadedTrack, 'Uploaded track should exist in database');
            $this->assertGreaterThan(0, $uploadedTrack->getDistance(), 'Track should have a distance');
            $this->assertGreaterThan(0, $uploadedTrack->getPoints(), 'Track should have points');
            $this->assertEquals(60, $uploadedTrack->getPoints(), 'Track should have 60 points');
            $this->assertNotNull($uploadedTrack->getStartDateTime(), 'Track should have a start time');
            $this->assertNotNull($uploadedTrack->getEndDateTime(), 'Track should have an end time');
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testTrackDurationIsCalculated(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $gpxFile = $this->generateGpxFile(53.55, 9.99, '2024-07-15T18:00:00Z', 60);

        try {
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');

            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile);

            $client->submit($form);

            $uploadedTrack = $this->getNewestTrackForRide($ride->getId());

            $this->assertNotNull($uploadedTrack, 'Uploaded track should exist');

            $durationSeconds = $uploadedTrack->getDurationInSeconds();
            // 60 points, 2 minutes apart = 118 minutes = 7080 seconds
            $this->assertGreaterThan(6000, $durationSeconds, 'Duration should be roughly 2 hours');
            $this->assertLessThan(8000, $durationSeconds, 'Duration should be roughly 2 hours');
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testTrackDistanceAndDurationShownOnRidePage(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('berlin');
        $this->assertNotNull($ride, 'Past Berlin ride fixture should exist');

        $gpxFile = $this->generateGpxFile(52.50, 13.42, '2024-08-01T18:00:00Z', 65);

        try {
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');
            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile);
            $client->submit($form);

            $crawler = $client->request('GET', $this->buildRideUrl($ride));
            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $tracksTabContent = $crawler->filter('#tracks')->text();

            $this->assertStringContainsString('km', $tracksTabContent, 'Track distance should be shown');
            $this->assertStringContainsString('km/h', $tracksTabContent, 'Track average speed should be shown');
            $this->assertStringContainsString('testuser', $tracksTabContent, 'Track uploader username should be shown');
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testMultipleTracksOnRidePage(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $gpxFile1 = $this->generateGpxFile(53.55, 9.99, '2024-09-01T19:00:00Z', 60, 0.01);
        $gpxFile2 = $this->generateGpxFile(53.56, 9.98, '2024-09-01T19:05:00Z', 70, 0.008);

        try {
            // Upload first track
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');
            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile1);
            $client->submit($form);
            $this->assertEquals(302, $client->getResponse()->getStatusCode());

            // Upload second track
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');
            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile2);
            $client->submit($form);
            $this->assertEquals(302, $client->getResponse()->getStatusCode());

            // Check ride page shows tracks
            $crawler = $client->request('GET', $this->buildRideUrl($ride));
            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            // Count track rows in the tracks table (excluding header)
            $trackDataRows = $crawler->filter('#tracks table tr')->reduce(function ($node) {
                return $node->filter('td')->count() > 0;
            });

            // Fixture tracks + our 2 uploaded tracks
            $this->assertGreaterThanOrEqual(2, $trackDataRows->count(), 'At least 2 track rows should be shown');

            // Verify distance values are shown
            $kmCount = 0;
            $crawler->filter('#tracks table td')->each(function ($node) use (&$kmCount) {
                if (str_contains($node->text(), 'km')) {
                    $kmCount++;
                }
            });
            $this->assertGreaterThanOrEqual(2, $kmCount, 'At least 2 tracks should show distance in km');
        } finally {
            @unlink($gpxFile1);
            @unlink($gpxFile2);
        }
    }

    public function testStatisticsAveragedForMultipleTracks(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('berlin');
        $this->assertNotNull($ride, 'Past Berlin ride fixture should exist');
        $rideId = $ride->getId();

        // Track 1: small radius (0.005), 55 points → short distance, ~108 min
        $gpxFile1 = $this->generateGpxFile(52.50, 13.42, '2024-10-15T18:00:00Z', 55, 0.005);
        // Track 2: large radius (0.02), 120 points → long distance, ~238 min
        $gpxFile2 = $this->generateGpxFile(52.50, 13.42, '2024-10-15T18:00:00Z', 120, 0.02);

        try {
            // Upload first track
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');
            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile1);
            $client->submit($form);
            $this->assertEquals(302, $client->getResponse()->getStatusCode());

            $track1 = $this->getNewestTrackForRide($rideId);
            $this->assertNotNull($track1, 'First track should exist after upload');
            $track1Distance = $track1->getDistance();
            $track1Id = $track1->getId();
            $this->assertEquals(55, $track1->getPoints(), 'Track 1 should have 55 points');

            // Upload second track
            $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/addtrack');
            $form = $crawler->selectButton('Track hochladen')->form();
            $this->uploadFileToForm($form, $gpxFile2);
            $client->submit($form);
            $this->assertEquals(302, $client->getResponse()->getStatusCode());

            $track2 = $this->getNewestTrackForRide($rideId);
            $this->assertNotNull($track2, 'Second track should exist after upload');
            $track2Distance = $track2->getDistance();
            $this->assertNotEquals($track1Id, $track2->getId(), 'Second track should be different from first');
            $this->assertEquals(120, $track2->getPoints(), 'Track 2 should have 120 points');

            // Tracks must have clearly different distances
            $this->assertGreaterThan($track1Distance, $track2Distance, 'Track with larger radius should have greater distance');

            // Load all estimates for this ride
            $em = static::getContainer()->get('doctrine')->getManager();
            $estimates = $em->getRepository(RideEstimate::class)
                ->createQueryBuilder('re')
                ->where('re.ride = :rideId')
                ->setParameter('rideId', $rideId)
                ->getQuery()
                ->getResult();

            $this->assertGreaterThanOrEqual(2, count($estimates), 'At least 2 ride estimates should exist');

            // Calculate expected averages from all estimates (same logic as RideEstimateCalculator)
            $totalDistance = 0.0;
            $distanceCount = 0;
            $totalDuration = 0.0;
            $durationCount = 0;

            foreach ($estimates as $estimate) {
                if ($estimate->getEstimatedDistance()) {
                    $totalDistance += $estimate->getEstimatedDistance();
                    $distanceCount++;
                }
                if ($estimate->getEstimatedDuration()) {
                    $totalDuration += $estimate->getEstimatedDuration();
                    $durationCount++;
                }
            }

            $expectedAvgDistance = $distanceCount > 0 ? $totalDistance / $distanceCount : 0;
            $expectedAvgDuration = $durationCount > 0 ? $totalDuration / $durationCount : 0;

            // Verify ride statistics match the calculated averages
            $updatedRide = $em->getRepository(Ride::class)->find($rideId);
            $this->assertNotNull($updatedRide);

            $this->assertEqualsWithDelta($expectedAvgDistance, $updatedRide->getEstimatedDistance(), 0.01,
                'Ride estimated distance should be average of all track estimates');
            $this->assertEqualsWithDelta($expectedAvgDuration, $updatedRide->getEstimatedDuration(), 0.01,
                'Ride estimated duration should be average of all track estimates');

            $this->assertGreaterThan(0, $updatedRide->getEstimatedDistance(), 'Estimated distance should be positive');
            $this->assertGreaterThan(0, $updatedRide->getEstimatedDuration(), 'Estimated duration should be positive');

            // Verify estimates have different distances (proving averaging is meaningful)
            $distances = array_filter(array_map(fn($e) => $e->getEstimatedDistance(), $estimates));
            $uniqueDistances = array_unique($distances);
            $this->assertGreaterThanOrEqual(2, count($uniqueDistances),
                'There should be estimates with different distances, proving averaging is meaningful');
        } finally {
            @unlink($gpxFile1);
            @unlink($gpxFile2);
        }
    }
}
