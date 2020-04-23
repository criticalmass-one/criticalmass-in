<?php declare(strict_types=1);

namespace Tests\Heatmap\ColorStyle;

use App\Criticalmass\Heatmap\ColorStyle\BlueRedColorStyle;
use Imagine\Image\Palette\RGB;
use PHPUnit\Framework\TestCase;

class BlueRedColorStyleTest extends TestCase
{
    public function testStartColor(): void
    {
        $blueRedColorStyle = new BlueRedColorStyle();

        $actualStartColor = $blueRedColorStyle->getStartColor();

        $expectedStartColor = (new RGB())->color([0, 0, 255]);

        $this->assertEquals($expectedStartColor, $actualStartColor);
    }

    public function testColorSteps(): void
    {
        $blueRedColorStyle = new BlueRedColorStyle();

        $color = (new RGB())->color([0, 0, 255]);

        $color = $blueRedColorStyle->colorize($color);

        $this->assertEquals((new RGB())->color([10, 0, 245]), $color);

        $color = $blueRedColorStyle->colorize($color);

        $this->assertEquals((new RGB())->color([20, 0, 235]), $color);
    }

    public function testBoundaries(): void
    {
        $blueRedColorStyle = new BlueRedColorStyle();

        $color = (new RGB())->color([245, 0, 10]);

        $color = $blueRedColorStyle->colorize($color);

        $this->assertEquals((new RGB())->color([255, 0, 0]), $color);

        $color = $blueRedColorStyle->colorize($color);

        $this->assertEquals((new RGB())->color([255, 0, 0]), $color);
    }
}