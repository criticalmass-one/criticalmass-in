<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\PhotoImportCandidate;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UnifiedUploadControllerTest extends AbstractControllerTestCase
{
    private const PAGE_URL = '/upload';
    private const UPLOAD_URL = '/upload/file';

    // A valid 1x1 JPEG; a unique marker is appended per upload so the file hash differs.
    private const MINIMAL_JPEG_BASE64 = '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAALCAABAAEBAREA/8QAFAABAAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AfwD/2Q==';

    public function testUnifiedUploadPageAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', self::PAGE_URL);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUnifiedUploadPageRedirectsAnonymous(): void
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
            $client->request('POST', self::UPLOAD_URL, ['_token' => 'invalid'], ['file' => $this->uploadedFile($gpxFile, 'application/gpx+xml')]);

            $this->assertEquals(403, $client->getResponse()->getStatusCode());
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testGpxWithoutMatchingRideGetsParked(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $token = $this->uploadToken($client);
        $gpxFile = $this->generateGpxFile();

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($gpxFile, 'application/gpx+xml')]);

            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $payload = json_decode((string) $client->getResponse()->getContent(), true);
            $this->assertEquals('parked', $payload['status']);
            $this->assertEquals('track', $payload['kind']);

            $candidate = $this->findTrackCandidate('testuser@criticalmass.in');
            $this->assertNotNull($candidate);
            $this->assertNull($candidate->getRide());
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testDuplicateTrackIsDetected(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $token = $this->uploadToken($client);
        $gpxFile = $this->generateGpxFile('2002-02-02T19:00:00Z');

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($gpxFile, 'application/gpx+xml')]);
            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($gpxFile, 'application/gpx+xml')]);
            $payload = json_decode((string) $client->getResponse()->getContent(), true);

            $this->assertEquals('duplicate', $payload['status']);
        } finally {
            @unlink($gpxFile);
        }
    }

    public function testUnsupportedFileTypeReturnsError(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $token = $this->uploadToken($client);

        $pdf = tempnam(sys_get_temp_dir(), 'doc') . '.pdf';
        file_put_contents($pdf, '%PDF-1.4 not really');

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($pdf, 'application/pdf')]);

            $this->assertEquals(422, $client->getResponse()->getStatusCode());
            $payload = json_decode((string) $client->getResponse()->getContent(), true);
            $this->assertEquals('error', $payload['status']);
        } finally {
            @unlink($pdf);
        }
    }

    public function testImageGetsStaged(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $token = $this->uploadToken($client);
        $jpeg = $this->generateJpegFile();

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($jpeg, 'image/jpeg')]);

            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $payload = json_decode((string) $client->getResponse()->getContent(), true);
            $this->assertEquals('staged', $payload['status']);
            $this->assertEquals('photo', $payload['kind']);

            $candidate = $this->findPhotoCandidate('testuser@criticalmass.in');
            $this->assertNotNull($candidate, 'A staged photo candidate should have been persisted');
            $this->assertNull($candidate->getRide(), 'A freshly staged photo has no ride yet');
        } finally {
            @unlink($jpeg);
        }
    }

    public function testInvalidImageReturnsError(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $token = $this->uploadToken($client);

        $fake = tempnam(sys_get_temp_dir(), 'fakeimg') . '.jpg';
        file_put_contents($fake, 'this is definitely not an image');

        try {
            $client->request('POST', self::UPLOAD_URL, ['_token' => $token], ['file' => $this->uploadedFile($fake, 'image/jpeg')]);

            $this->assertEquals(422, $client->getResponse()->getStatusCode());
            $payload = json_decode((string) $client->getResponse()->getContent(), true);
            $this->assertEquals('error', $payload['status']);
        } finally {
            @unlink($fake);
        }
    }

    private function uploadToken(\Symfony\Bundle\FrameworkBundle\KernelBrowser $client): string
    {
        $crawler = $client->request('GET', self::PAGE_URL);
        $html = $crawler->html();

        $this->assertMatchesRegularExpression('/data-unified-upload-csrf-token-value="([^"]+)"/', $html);
        preg_match('/data-unified-upload-csrf-token-value="([^"]+)"/', $html, $matches);

        return $matches[1];
    }

    /**
     * Small circular GPX track at a date without a ride fixture (so it gets parked).
     * A unique marker keeps the file hash distinct per run for the dedup check.
     */
    private function generateGpxFile(string $startTime = '2001-01-01T19:00:00Z', float $lat = 41.40, float $lon = 2.17): string
    {
        $marker = uniqid('run', true);

        $gpx = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $gpx .= sprintf('<gpx version="1.1" creator="test-%s"><trk><name>Unified Test</name><trkseg>' . "\n", $marker);

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

        $tmpFile = tempnam(sys_get_temp_dir(), 'unified_gpx_') . '.gpx';
        file_put_contents($tmpFile, $gpx);

        return $tmpFile;
    }

    private function generateJpegFile(): string
    {
        // Valid JPEG bytes plus a unique trailing marker → distinct hash per run, header
        // stays intact so the image still decodes.
        $bytes = base64_decode(self::MINIMAL_JPEG_BASE64) . "\n<!--" . uniqid('img', true) . "-->";

        $tmpFile = tempnam(sys_get_temp_dir(), 'unified_jpg_') . '.jpg';
        file_put_contents($tmpFile, $bytes);

        return $tmpFile;
    }

    private function uploadedFile(string $path, string $mimeType): UploadedFile
    {
        return new UploadedFile($path, basename($path), $mimeType, null, true);
    }

    private function findTrackCandidate(string $email): ?TrackImportCandidate
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        return $em->getRepository(TrackImportCandidate::class)->findOneBy([
            'user' => $user,
            'source' => TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD,
        ], ['id' => 'DESC']);
    }

    private function findPhotoCandidate(string $email): ?PhotoImportCandidate
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        return $em->getRepository(PhotoImportCandidate::class)->findOneBy([
            'user' => $user,
        ], ['id' => 'DESC']);
    }
}
