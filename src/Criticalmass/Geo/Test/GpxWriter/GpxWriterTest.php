<?php

namespace Caldera\GeoBundle\Test\DistanceCalculator;

use Caldera\GeoBundle\GpxWriter\GpxWriter;
use PHPUnit\Framework\TestCase;

class GpxWriterTest extends TestCase
{
    public function testGpxWriter1()
    {
        $gpxWriter = new GpxWriter();

        $gpxWriter->generateGpxContent();
    }
}
