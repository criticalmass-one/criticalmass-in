<?php declare(strict_types=1);

namespace Tests\Heatmap\Pipette;

use App\Criticalmass\Heatmap\Pipette\AveragePipette;
use App\Criticalmass\Heatmap\Tile\Tile;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use PHPUnit\Framework\TestCase;

class AveragePipetteTest extends TestCase
{
    public function testFoo(): void
    {
        $tile = $this->createMock(Tile::class);

        $image = $this->createMock(ImageInterface::class);

        $tile
            ->method('oldImage')
            ->will($this->returnValue($image));

        $color = $this->createMock(ColorInterface::class);

        $image
            ->method('getColorAt')
            ->will($this->returnValue($color));

        $color
            ->method('getValue')
            ->willReturnOnConsecutiveCalls(
                $this->createColor(255, 0, 0),
                $this->createColor(0, 255, 0),
                $this->createColor(0, 0, 255)
            );
        
        $point = new Point(5, 5);

        $actualColor = AveragePipette::getColor($tile, $point);

        $this->assertEquals($this->createColor(200, 200, 0), $actualColor);
    }

    protected function createColor(int $red, int $green, int $blue): ColorInterface
    {
        return (new RGB())->color([$red, $green, $blue]);
    }
}