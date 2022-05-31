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
    public function testSameColor(): void
    {
        $image = $this->createMock(ImageInterface::class);
        $tile = new Tile(5, 5, 10);
        $tile->setOldImage($image);

        $color = $this->createColor(255, 127, 0);

        $image
            ->expects($this->exactly(9))
            ->method($this->equalTo('getColorAt'))
            ->will($this->returnValue($color));

        $point = new Point(5, 5);

        $actualColor = AveragePipette::getColor($tile, $point);

        $this->assertEquals($this->createColor(255, 127, 0), $actualColor);
    }

    public function testDifferentColors(): void
    {
        $color1 = $this->createColor(11, 12, 13);
        $color2 = $this->createColor(21, 22, 23);
        $color3 = $this->createColor(31, 32, 33);
        $color4 = $this->createColor(41, 42, 43);
        $color5 = $this->createColor(51, 52, 53);
        $color6 = $this->createColor(61, 62, 63);
        $color7 = $this->createColor(71, 72, 73);
        $color8 = $this->createColor(81, 82, 83);
        $color9 = $this->createColor(91, 92, 93);

        $image = $this->createMock(ImageInterface::class);
        $tile = new Tile(5, 5, 10);
        $tile->setOldImage($image);

        $image
            ->expects($this->exactly(9))
            ->method($this->equalTo('getColorAt'))
            ->will($this->onConsecutiveCalls(
                $color1,
                $color2,
                $color3,
                $color4,
                $color5,
                $color6,
                $color7,
                $color8,
                $color9
            ));

        $point = new Point(5, 5);

        $actualColor = AveragePipette::getColor($tile, $point);

        $this->assertEquals($this->createColor(51, 52, 53), $actualColor);
    }

    public function testTransparentBackgroundReturnsNull(): void
    {
        $image = $this->createMock(ImageInterface::class);
        $tile = new Tile(5, 5, 10);
        $tile->setOldImage($image);

        $color = $this->createColor(255, 127, 0, 0);

        $image
            ->expects($this->exactly(9))
            ->method($this->equalTo('getColorAt'))
            ->will($this->returnValue($color));

        $point = new Point(5, 5);

        $this->assertNull(AveragePipette::getColor($tile, $point));
    }

    protected function createColor(int $red, int $green, int $blue, int $alpha = 100): ColorInterface
    {
        return (new RGB())->color([$red, $green, $blue], $alpha);
    }
}