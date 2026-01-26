<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Entity\Track;
use App\Twig\Extension\VelocityExtension;
use PHPUnit\Framework\TestCase;

class VelocityExtensionTest extends TestCase
{
    private VelocityExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new VelocityExtension();
    }

    private function createTrack(?float $distance, ?\DateTime $start, ?\DateTime $end): Track
    {
        $track = $this->createMock(Track::class);
        $track->method('getDistance')->willReturn($distance);
        $track->method('getStartDateTime')->willReturn($start);
        $track->method('getEndDateTime')->willReturn($end);

        return $track;
    }

    public function testCalculatesVelocity(): void
    {
        $track = $this->createTrack(
            15.0,
            new \DateTime('2024-01-15 19:00:00'),
            new \DateTime('2024-01-15 21:00:00')
        );

        $velocity = $this->extension->averageVelocity($track);

        $this->assertNotNull($velocity);
        $this->assertEqualsWithDelta(7.5, $velocity, 0.1);
    }

    public function testCalculatesVelocityForOneHour(): void
    {
        $track = $this->createTrack(
            20.0,
            new \DateTime('2024-01-15 19:00:00'),
            new \DateTime('2024-01-15 20:00:00')
        );

        $velocity = $this->extension->averageVelocity($track);

        $this->assertEqualsWithDelta(20.0, $velocity, 0.1);
    }

    public function testReturnsNullWhenStartDateTimeMissing(): void
    {
        $track = $this->createTrack(
            15.0,
            null,
            new \DateTime('2024-01-15 21:00:00')
        );

        $this->assertNull($this->extension->averageVelocity($track));
    }

    public function testReturnsNullWhenEndDateTimeMissing(): void
    {
        $track = $this->createTrack(
            15.0,
            new \DateTime('2024-01-15 19:00:00'),
            null
        );

        $this->assertNull($this->extension->averageVelocity($track));
    }

    public function testReturnsNullWhenDistanceMissing(): void
    {
        $track = $this->createTrack(
            null,
            new \DateTime('2024-01-15 19:00:00'),
            new \DateTime('2024-01-15 21:00:00')
        );

        $this->assertNull($this->extension->averageVelocity($track));
    }

    public function testReturnsNullWhenAllDataMissing(): void
    {
        $track = $this->createTrack(null, null, null);

        $this->assertNull($this->extension->averageVelocity($track));
    }

    public function testGetName(): void
    {
        $this->assertEquals(VelocityExtension::class, $this->extension->getName());
    }

    public function testGetFunctionsReturnsArray(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
    }
}
