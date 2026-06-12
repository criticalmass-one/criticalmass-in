<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\TrackImportCandidate;
use App\Entity\User;

class BulkTrackUploadControllerTest extends AbstractControllerTestCase
{
    private const UPLOAD_URL = '/trackupload/bulk/file';
    private const PAGE_URL = '/trackupload/bulk';

    /**
     * Generates a small circular GPX track at a date that has no ride fixture, so the
     * decider cannot match it and the candidate is parked for review.
     */
    private function generateGpxFile(string $startTime = '2001-01-01T19:00:00Z', float $lat = 41.40, float $lon = 2.17): string
    {
        // A unique marker keeps the file hash distinct per run, so the dedup check does
        // not flag re-runs as duplicates of candidates left behind by earlier test runs.
        $marker = uniqid('run', true);

        $gpx = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $gpx .= sprintf('<gpx version="1.1" creator="test-%s"><trk><name>Bulk Test</name><trkseg>' . "\n", $marker);

        $dateTime = new \DateTime($startTime);

        for ($i = 0; $i < 30; $i++) {
            $angle = 2 * M_PI * $i / 30;
            $gpx .= sprintf(
                '<trkpt lat="%.6f" lon="%.6f"><time>%s</time></trkpt>' . "\n",
                $lat + sin($angle) * 0.01,
                $lon + cos($angle) * 0.01,
                $dateTime->format('Y-m-d\TH:i:s\Z'),
            );
            $dateTime->modify('+2 minutes');
        }

        $gpx .= '</trkseg></trk></gpx>';

        $tmpFile = tempnam(sys_get_temp_dir(), 'bulk_gpx_') . '.gpx';
        file_put_contents($tmpFile, $gpx);

        return $tmpFile;
    }

    private function extractUploadToken(string $html): string
    {
        $this->assertMatchesRegularExpression('/data-bulk-upload-csrf-token-value="([^"]+)"/', $html);
        preg_match('/data-bulk-upload-csrf-token-value="([^"]+)"/', $html, $matches);

        return $matches[1];
    }

    public function testBulkUploadPageAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', self::PAGE_URL);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testBulkUploadPageRedirectsAnonymous(): void
    {
        $client = static::createClient();

        $client->request('GET', self::PAGE_URL);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testFileUploadRejectsInvalidCsrfToken(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $gpxFile = $this->generateGpxFile();

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => 'invalid'], [
                'file' => $this->uploadedFile($gpxFile),
            ]);

            $this->assertEquals(403, $client->getResponse()->getStatusCode());
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testGpxWithoutMatchingRideGetsParked(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', self::PAGE_URL);
        $token = $this->extractUploadToken($crawler->html());

        $gpxFile = $this->generateGpxFile();

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], [
                'file' => $this->uploadedFile($gpxFile),
            ]);

            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $payload = json_decode((string) $client->getResponse()->getContent(), true);
            $this->assertEquals('parked', $payload['status']);

            $candidate = $this->findUploadedCandidate('testuser@criticalmass.in');
            $this->assertNotNull($candidate, 'A parked upload candidate should have been persisted');
            $this->assertNull($candidate->getRide(), 'A parked candidate has no ride');
            $this->assertEquals(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD, $candidate->getSource());
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testDuplicateUploadIsDetected(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', self::PAGE_URL);
        $token = $this->extractUploadToken($crawler->html());

        $gpxFile = $this->generateGpxFile('2002-02-02T19:00:00Z');

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($gpxFile)]);
            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($gpxFile)]);
            $payload = json_decode((string) $client->getResponse()->getContent(), true);

            $this->assertEquals('duplicate', $payload['status']);
        } finally {
            @unlink($gpxFile);
        }
    }

    private function uploadedFile(string $path): \Symfony\Component\HttpFoundation\File\UploadedFile
    {
        return new \Symfony\Component\HttpFoundation\File\UploadedFile($path, basename($path), 'application/gpx+xml', null, true);
    }

    private function findUploadedCandidate(string $email): ?TrackImportCandidate
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        return $em->getRepository(TrackImportCandidate::class)->findOneBy([
            'user' => $user,
            'source' => TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD,
        ], ['id' => 'DESC']);
    }
}
