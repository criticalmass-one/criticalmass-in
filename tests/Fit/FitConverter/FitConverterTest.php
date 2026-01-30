<?php declare(strict_types=1);

namespace Tests\Fit\FitConverter;

use App\Criticalmass\Fit\FitConverter\FitConverter;
use App\Criticalmass\Fit\FitParser\FitParser;
use App\Criticalmass\Geo\GpxService\GpxService;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\InvalidFitFileException;
use phpGPX\phpGPX;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class FitConverterTest extends TestCase
{
    private FitConverter $fitConverter;
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/../../Fixtures/Fit';

        $fitParser = new FitParser();
        $gpxService = new GpxService(new ParameterBag([]));

        $this->fitConverter = new FitConverter($fitParser, $gpxService);
    }

    public static function fitFileProvider(): array
    {
        $fixturesDir = __DIR__ . '/../../Fixtures/Fit';

        $files = glob($fixturesDir . '/*.fit');

        $data = [];
        foreach ($files as $file) {
            $data[basename($file)] = [$file];
        }

        return $data;
    }

    private function getFirstFitFile(): string
    {
        $files = glob($this->fixturesDir . '/*.fit');

        return $files[0];
    }

    public function testConvertToGpxStringReturnsString(): void
    {
        $result = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testConvertToGpxStringReturnsValidXml(): void
    {
        $result = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $xml = simplexml_load_string($result);

        $this->assertNotFalse($xml);
        $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
    }

    public function testConvertToGpxStringContainsGpxStructure(): void
    {
        $result = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $this->assertStringContainsString('<gpx', $result);
        $this->assertStringContainsString('<trk>', $result);
        $this->assertStringContainsString('<trkseg>', $result);
        $this->assertStringContainsString('<trkpt', $result);
    }

    public function testConvertToGpxStringContainsCoordinates(): void
    {
        $result = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $this->assertStringContainsString('lat=', $result);
        $this->assertStringContainsString('lon=', $result);
    }

    public function testConvertToGpxStringContainsTimeElements(): void
    {
        $result = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $this->assertStringContainsString('<time>', $result);
    }

    public function testConvertToGpxStringContainsElevation(): void
    {
        $result = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $this->assertStringContainsString('<ele>', $result);
    }

    public function testConvertedGpxCanBeLoadedByPhpGpx(): void
    {
        $gpxString = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $tmpFile = tempnam(sys_get_temp_dir(), 'gpx_test_');
        file_put_contents($tmpFile, $gpxString);

        try {
            $phpGpx = new phpGPX();
            $gpxFile = $phpGpx->load($tmpFile);

            $this->assertCount(1, $gpxFile->tracks);
            $this->assertGreaterThan(0, count($gpxFile->tracks[0]->getPoints()));
        } finally {
            unlink($tmpFile);
        }
    }

    public function testConvertedGpxHasReasonablePointCount(): void
    {
        $gpxString = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $tmpFile = tempnam(sys_get_temp_dir(), 'gpx_test_');
        file_put_contents($tmpFile, $gpxString);

        try {
            $phpGpx = new phpGPX();
            $gpxFile = $phpGpx->load($tmpFile);

            $this->assertGreaterThanOrEqual(50, count($gpxFile->tracks[0]->getPoints()));
        } finally {
            unlink($tmpFile);
        }
    }

    public function testConvertedGpxCoordinatesAreValid(): void
    {
        $gpxString = $this->fitConverter->convertToGpxString($this->getFirstFitFile());

        $tmpFile = tempnam(sys_get_temp_dir(), 'gpx_test_');
        file_put_contents($tmpFile, $gpxString);

        try {
            $phpGpx = new phpGPX();
            $gpxFile = $phpGpx->load($tmpFile);

            foreach ($gpxFile->tracks[0]->getPoints() as $point) {
                $this->assertGreaterThanOrEqual(-90, $point->latitude);
                $this->assertLessThanOrEqual(90, $point->latitude);
                $this->assertGreaterThanOrEqual(-180, $point->longitude);
                $this->assertLessThanOrEqual(180, $point->longitude);
            }
        } finally {
            unlink($tmpFile);
        }
    }

    /**
     * @dataProvider fitFileProvider
     */
    public function testConvertWithMultipleFitFiles(string $filePath): void
    {
        $result = $this->fitConverter->convertToGpxString($filePath);

        $this->assertNotEmpty($result);

        $xml = simplexml_load_string($result);

        $this->assertNotFalse($xml);
    }

    public function testConvertThrowsExceptionForInvalidFile(): void
    {
        $this->expectException(InvalidFitFileException::class);

        $tmpFile = tempnam(sys_get_temp_dir(), 'fit_test_');
        file_put_contents($tmpFile, 'Not a FIT file');

        try {
            $this->fitConverter->convertToGpxString($tmpFile);
        } finally {
            unlink($tmpFile);
        }
    }
}
