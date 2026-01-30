<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\Track;
use PHPUnit\Framework\TestCase;

class TrackTest extends TestCase
{
    public function testTrackSourceFitConstantExists(): void
    {
        $this->assertSame('TRACK_SOURCE_FIT', Track::TRACK_SOURCE_FIT);
    }

    public function testSetSourceAcceptsFit(): void
    {
        $track = new Track();
        $track->setSource(Track::TRACK_SOURCE_FIT);

        $this->assertSame(Track::TRACK_SOURCE_FIT, $track->getSource());
    }
}
