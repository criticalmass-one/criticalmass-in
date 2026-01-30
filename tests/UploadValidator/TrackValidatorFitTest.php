<?php declare(strict_types=1);

namespace Tests\UploadValidator;

use App\Criticalmass\UploadValidator\TrackValidator;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\InvalidFitFileException;
use App\Entity\Track;
use PHPUnit\Framework\TestCase;

class TrackValidatorFitTest extends TestCase
{
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/../Fixtures/Fit';
    }

    private function getFirstFitFile(): string
    {
        $files = glob($this->fixturesDir . '/*.fit');

        return $files[0];
    }

    private function createLoadedValidator(string $fileContent, string $filename): TrackValidator
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn($filename);

        $reflClass = new \ReflectionClass(TrackValidator::class);
        $validator = $reflClass->newInstanceWithoutConstructor();

        $trackProp = $reflClass->getProperty('track');
        $trackProp->setValue($validator, $track);

        $contentProp = $reflClass->getProperty('rawFileContent');
        $contentProp->setValue($validator, $fileContent);

        return $validator;
    }

    public function testIsFitFileDetectsValidFitFile(): void
    {
        $validator = $this->createLoadedValidator(
            file_get_contents($this->getFirstFitFile()),
            'test.fit'
        );

        $this->assertTrue($validator->isFitFile());
    }

    public function testIsFitFileRejectsGpxFile(): void
    {
        $gpxContent = '<?xml version="1.0"?><gpx><trk><trkseg><trkpt lat="52.0" lon="10.0"><time>2024-01-01T00:00:00Z</time></trkpt></trkseg></trk></gpx>';

        $validator = $this->createLoadedValidator($gpxContent, 'test.gpx');

        $this->assertFalse($validator->isFitFile());
    }

    public function testValidateAcceptsFitFile(): void
    {
        $validator = $this->createLoadedValidator(
            file_get_contents($this->getFirstFitFile()),
            'test.fit'
        );

        $this->assertTrue($validator->validate());
    }

    public function testValidateAcceptsGpxFile(): void
    {
        $gpxPath = __DIR__ . '/../PhotoGps/data/braunschweig.gpx';

        if (!file_exists($gpxPath)) {
            $this->markTestSkipped('GPX test fixture not available.');
        }

        $validator = $this->createLoadedValidator(
            file_get_contents($gpxPath),
            'test.gpx'
        );

        $this->assertTrue($validator->validate());
    }

    public function testValidateRejectsTruncatedFitFile(): void
    {
        $this->expectException(InvalidFitFileException::class);

        $fitContent = file_get_contents($this->getFirstFitFile());
        $truncatedContent = substr($fitContent, 0, 10);

        $validator = $this->createLoadedValidator($truncatedContent, 'truncated.fit');

        $validator->validate();
    }

    public function testValidateRejectsRandomBinaryFile(): void
    {
        $this->expectException(InvalidFitFileException::class);

        $randomContent = random_bytes(100);

        $validator = $this->createLoadedValidator($randomContent, 'random.fit');

        $validator->validate();
    }
}
