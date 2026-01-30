<?php declare(strict_types=1);

namespace Tests\Fit\FitParser;

use App\Criticalmass\Fit\FitParser\FitData;
use App\Criticalmass\Fit\FitParser\FitParser;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\InvalidFitFileException;
use PHPUnit\Framework\TestCase;

class FitParserTest extends TestCase
{
    private FitParser $fitParser;
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fitParser = new FitParser();
        $this->fixturesDir = __DIR__ . '/../../Fixtures/Fit';
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

    public function testParseReturnsFitData(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        $this->assertInstanceOf(FitData::class, $result);
    }

    public function testParseExtractsLatLngData(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        $latLngData = $result->getLatLngData();

        $this->assertNotEmpty($latLngData);
        $this->assertCount(2, $latLngData[0]);
        $this->assertIsFloat($latLngData[0][0]);
        $this->assertIsFloat($latLngData[0][1]);
    }

    public function testParseLatLngValuesAreValidCoordinates(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        foreach ($result->getLatLngData() as [$lat, $lng]) {
            $this->assertGreaterThanOrEqual(-90, $lat);
            $this->assertLessThanOrEqual(90, $lat);
            $this->assertGreaterThanOrEqual(-180, $lng);
            $this->assertLessThanOrEqual(180, $lng);
        }
    }

    public function testParseExtractsAltitudeData(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        $altitudeData = $result->getAltitudeData();

        $this->assertNotEmpty($altitudeData);
        $this->assertIsFloat($altitudeData[0]);
    }

    public function testParseExtractsTimeData(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        $timeData = $result->getTimeData();

        $this->assertNotEmpty($timeData);
        $this->assertSame(0, $timeData[0]);

        for ($i = 1; $i < count($timeData); $i++) {
            $this->assertGreaterThanOrEqual($timeData[$i - 1], $timeData[$i]);
        }
    }

    public function testParseExtractsStartDateTime(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        $this->assertInstanceOf(\DateTime::class, $result->getStartDateTime());
    }

    public function testParseDataArraysHaveSameLength(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        $latLngCount = count($result->getLatLngData());
        $altitudeCount = count($result->getAltitudeData());
        $timeCount = count($result->getTimeData());

        $this->assertSame($latLngCount, $altitudeCount);
        $this->assertSame($latLngCount, $timeCount);
    }

    public function testParseFiltersZeroCoordinates(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        foreach ($result->getLatLngData() as [$lat, $lng]) {
            $this->assertFalse($lat == 0 && $lng == 0, 'Zero coordinates should be filtered out');
        }
    }

    /**
     * @dataProvider fitFileProvider
     */
    public function testParseWithMultipleFitFiles(string $filePath): void
    {
        $result = $this->fitParser->parse($filePath);

        $this->assertInstanceOf(FitData::class, $result);
        $this->assertNotEmpty($result->getLatLngData());
    }

    public function testParseThrowsExceptionForInvalidFile(): void
    {
        $this->expectException(InvalidFitFileException::class);

        $tmpFile = tempnam(sys_get_temp_dir(), 'fit_test_');
        file_put_contents($tmpFile, 'This is not a FIT file');

        try {
            $this->fitParser->parse($tmpFile);
        } finally {
            unlink($tmpFile);
        }
    }

    #[\PHPUnit\Framework\Attributes\WithoutErrorHandler]
    public function testParseThrowsExceptionForEmptyFile(): void
    {
        $this->expectException(InvalidFitFileException::class);

        $tmpFile = tempnam(sys_get_temp_dir(), 'fit_test_');
        file_put_contents($tmpFile, '');

        try {
            $this->fitParser->parse($tmpFile);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testParseMinimumPointCount(): void
    {
        $result = $this->fitParser->parse($this->getFirstFitFile());

        $this->assertGreaterThanOrEqual(50, count($result->getLatLngData()));
    }
}
