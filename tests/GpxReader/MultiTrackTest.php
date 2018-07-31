<?php declare(strict_types=1);

namespace Tests\GpxReader;

use App\Criticalmass\Geo\GpxReader\GpxReader;
use PHPUnit\Framework\TestCase;

class MultiTrackTest extends TestCase
{
    public function test1(): void
    {
        $gpxTestFilename = __DIR__.'/../GpxReader/Files/cmhh.gpx';

        $gpxReader = new GpxReader();
        $gpxReader
            ->loadFromFile($gpxTestFilename);

        $this->assertEquals(5218, $gpxReader->countPoints());
    }

    public function test2(): void
    {
        $gpxTestFilename = __DIR__.'/../GpxReader/Files/berlin.gpx';

        $gpxReader = new GpxReader();
        $gpxReader
            ->loadFromFile($gpxTestFilename);

        $this->assertEquals(3325, $gpxReader->countPoints());
    }
}
