<?php declare(strict_types=1);

namespace Tests\Heatmap\FilenameGenerator;

use App\Criticalmass\Heatmap\FilenameGenerator\FilenameGenerator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Tile\Tile;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class FilenameGeneratorTest extends TestCase
{
    public function testFilenameGenerator(): void
    {
        $heatmap = new Class implements HeatmapInterface
        {
            public function getIdentifier(): string
            {
                return 'testidentifier';
            }

            public function getUser(): ?User
            {
            }

            public function getCity(): ?City
            {
            }

            public function getRide(): ?Ride
            {
            }

            public function getTracks(): Collection
            {
            }
        };

        $tile = new Tile(21, 42, 13);

        $actualFilename = FilenameGenerator::generate($heatmap, $tile);
        $expectedFilename = 'testidentifier/13/21/42.png';

        $this->assertEquals($expectedFilename, $actualFilename);
    }
}