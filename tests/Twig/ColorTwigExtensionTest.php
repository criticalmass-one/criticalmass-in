<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Twig\Extension\ColorTwigExtension;
use PHPUnit\Framework\TestCase;

class ColorTwigExtensionTest extends TestCase
{
    private ColorTwigExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new ColorTwigExtension();
    }

    public function testBlack(): void
    {
        $this->assertEquals('#000000', $this->extension->rgbToHex(0, 0, 0));
    }

    public function testWhite(): void
    {
        $this->assertEquals('#ffffff', $this->extension->rgbToHex(255, 255, 255));
    }

    public function testRed(): void
    {
        $this->assertEquals('#ff0000', $this->extension->rgbToHex(255, 0, 0));
    }

    public function testGreen(): void
    {
        $this->assertEquals('#00ff00', $this->extension->rgbToHex(0, 255, 0));
    }

    public function testBlue(): void
    {
        $this->assertEquals('#0000ff', $this->extension->rgbToHex(0, 0, 255));
    }

    public function testCustomColor(): void
    {
        $this->assertEquals('#1a2b3c', $this->extension->rgbToHex(26, 43, 60));
    }

    public function testLeadingZeros(): void
    {
        $this->assertEquals('#010203', $this->extension->rgbToHex(1, 2, 3));
    }

    public function testMidGray(): void
    {
        $this->assertEquals('#808080', $this->extension->rgbToHex(128, 128, 128));
    }

    public function testGetFunctionsReturnsArray(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
    }

    public function testGetName(): void
    {
        $this->assertEquals('color_extension', $this->extension->getName());
    }
}
