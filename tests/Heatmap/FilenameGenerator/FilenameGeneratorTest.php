<?php declare(strict_types=1);

namespace Tests\Heatmap\FilenameGenerator;

use App\Criticalmass\Heatmap\FilenameGenerator\FilenameGenerator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Tile\Tile;
use PHPUnit\Framework\TestCase;

class FilenameGeneratorTest extends TestCase
{
    public function testFilenameGenerator(): void
    {
        $heatmap = $this->createMock(HeatmapInterface::class);
        $heatmap
            ->expects($this->once())
            ->method($this->equalTo('getIdentifier'))
            ->will($this->returnValue('testidentifier'));

        $tile = new Tile(21, 42, 13);

        $actualFilename = FilenameGenerator::generate($heatmap, $tile);
        $expectedFilename = 'testidentifier/13/21/42.png';

        $this->assertEquals($expectedFilename, $actualFilename);
    }
}