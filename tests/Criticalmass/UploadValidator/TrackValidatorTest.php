<?php declare(strict_types=1);

namespace App\Tests\Criticalmass\UploadValidator;

use App\Criticalmass\UploadValidator\TrackValidator;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoDateTimeException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoLatitudeLongitudeException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NotEnoughCoordsException;
use PHPUnit\Framework\TestCase;

class TrackValidatorTest extends TestCase
{
    private function createValidatorWithGpx(string $gpxContent): TrackValidator
    {
        $reflection = new \ReflectionClass(TrackValidator::class);
        $validator = $reflection->newInstanceWithoutConstructor();

        $simpleXmlProperty = $reflection->getProperty('simpleXml');
        $simpleXmlProperty->setValue($validator, new \SimpleXMLElement($gpxContent));

        $rawFileContentProperty = $reflection->getProperty('rawFileContent');
        $rawFileContentProperty->setValue($validator, $gpxContent);

        return $validator;
    }

    private function generateTrkpt(int $count, float $startLat = 53.0, float $startLon = 10.0): string
    {
        $points = '';
        for ($i = 0; $i < $count; $i++) {
            $lat = $startLat + ($i * 0.001);
            $lon = $startLon + ($i * 0.001);
            $time = (new \DateTime('2024-01-01 12:00:00'))->modify("+{$i} seconds")->format('Y-m-d\TH:i:s\Z');
            $points .= sprintf('<trkpt lat="%.6f" lon="%.6f"><time>%s</time></trkpt>', $lat, $lon, $time);
        }

        return $points;
    }

    private function buildGpx(string $trackContent): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><gpx version="1.1"><trk>' . $trackContent . '</trk></gpx>';
    }

    public function testValidateSingleSegment(): void
    {
        $gpx = $this->buildGpx('<trkseg>' . $this->generateTrkpt(60) . '</trkseg>');
        $validator = $this->createValidatorWithGpx($gpx);

        $this->assertTrue($validator->validate());
    }

    public function testValidateMultipleSegments(): void
    {
        $gpx = $this->buildGpx(
            '<trkseg>' . $this->generateTrkpt(30, 53.0, 10.0) . '</trkseg>'
            . '<trkseg>' . $this->generateTrkpt(30, 54.0, 11.0) . '</trkseg>'
        );
        $validator = $this->createValidatorWithGpx($gpx);

        $this->assertTrue($validator->validate());
    }

    public function testMultipleSegmentsWithTooFewPointsThrowsException(): void
    {
        $gpx = $this->buildGpx(
            '<trkseg>' . $this->generateTrkpt(20, 53.0, 10.0) . '</trkseg>'
            . '<trkseg>' . $this->generateTrkpt(20, 54.0, 11.0) . '</trkseg>'
        );
        $validator = $this->createValidatorWithGpx($gpx);

        $this->expectException(NotEnoughCoordsException::class);
        $validator->validate();
    }

    public function testMultipleSegmentsWithInvalidLatLonInSecondSegmentThrowsException(): void
    {
        $gpx = $this->buildGpx(
            '<trkseg>' . $this->generateTrkpt(30, 53.0, 10.0) . '</trkseg>'
            . '<trkseg><trkpt lat="invalid" lon="10.0"><time>2024-01-01T12:00:00Z</time></trkpt>'
            . $this->generateTrkpt(29, 54.0, 11.0) . '</trkseg>'
        );
        $validator = $this->createValidatorWithGpx($gpx);

        $this->expectException(NoLatitudeLongitudeException::class);
        $validator->validate();
    }

    public function testMultipleSegmentsWithMissingDateTimeInSecondSegmentThrowsException(): void
    {
        $gpx = $this->buildGpx(
            '<trkseg>' . $this->generateTrkpt(30, 53.0, 10.0) . '</trkseg>'
            . '<trkseg><trkpt lat="54.000000" lon="11.000000"></trkpt>'
            . $this->generateTrkpt(29, 54.001, 11.001) . '</trkseg>'
        );
        $validator = $this->createValidatorWithGpx($gpx);

        $this->expectException(NoDateTimeException::class);
        $validator->validate();
    }
}
