<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Twig\Extension\DurationTwigExtension;
use PHPUnit\Framework\TestCase;

class DurationTwigExtensionTest extends TestCase
{
    private DurationTwigExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new DurationTwigExtension();
    }

    public function testReturnsNullForNullInput(): void
    {
        $this->assertNull($this->extension->duration(null));
    }

    public function testReturnsNullForEmptyString(): void
    {
        $this->assertNull($this->extension->duration(''));
    }

    public function testHoursAndMinutes(): void
    {
        $this->assertEquals("1\u{00A0}h 30\u{00A0}min", $this->extension->duration('5400'));
    }

    public function testExactHours(): void
    {
        $this->assertEquals("1\u{00A0}h", $this->extension->duration('3600'));
    }

    public function testMinutesOnly(): void
    {
        $this->assertEquals("45\u{00A0}min", $this->extension->duration('2700'));
    }

    public function testSecondsIgnored(): void
    {
        $this->assertEquals("1\u{00A0}h 1\u{00A0}min", $this->extension->duration('3661'));
    }

    public function testLessThanOneMinute(): void
    {
        $this->assertEquals("0\u{00A0}min", $this->extension->duration('30'));
    }

    public function testMultipleHours(): void
    {
        $this->assertEquals("2\u{00A0}h 15\u{00A0}min", $this->extension->duration('8100'));
    }

    public function testGetName(): void
    {
        $this->assertEquals('duration_extension', $this->extension->getName());
    }

    public function testGetFunctionsReturnsArray(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
    }
}
