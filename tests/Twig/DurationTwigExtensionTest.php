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

    public function testHumanizesDuration(): void
    {
        $result = $this->extension->duration('3600');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
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
