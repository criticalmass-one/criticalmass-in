<?php declare(strict_types=1);

namespace Tests\DistanceCalculator;

use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use PHPUnit\Framework\TestCase;

class GpxWriterTest extends TestCase
{
    public function testGpxWriter1(): void
    {
        $gpxWriter = new GpxWriter();

        $gpxWriter->generateGpxContent();
    }
}
