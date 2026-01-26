<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use App\Twig\Extension\SocialNetworkTwigExtension;
use PHPUnit\Framework\TestCase;

class SocialNetworkTwigExtensionTrimIntroTest extends TestCase
{
    private SocialNetworkTwigExtension $extension;

    protected function setUp(): void
    {
        $networkManager = $this->createMock(NetworkManagerInterface::class);
        $this->extension = new SocialNetworkTwigExtension($networkManager);
    }

    public function testShortTextRemainsUnchanged(): void
    {
        $text = 'This is a short text.';

        $this->assertEquals($text, $this->extension->trimIntro($text));
    }

    public function testExactLengthTextRemainsUnchanged(): void
    {
        $text = str_repeat('a', 350);

        $this->assertEquals($text, $this->extension->trimIntro($text));
    }

    public function testLongTextIsTrimmedAtPeriod(): void
    {
        $text = str_repeat('a', 351) . '. More text here.';

        $result = $this->extension->trimIntro($text);

        $this->assertStringEndsWith('.', $result);
        $this->assertLessThan(strlen($text), strlen($result));
    }

    public function testLongTextIsTrimmedAtExclamationMark(): void
    {
        $text = str_repeat('a', 351) . '! More text here.';

        $result = $this->extension->trimIntro($text);

        $this->assertStringEndsWith('!', $result);
    }

    public function testLongTextIsTrimmedAtQuestionMark(): void
    {
        $text = str_repeat('a', 351) . '? More text here.';

        $result = $this->extension->trimIntro($text);

        $this->assertStringEndsWith('?', $result);
    }

    public function testLongTextIsTrimmedAtSemicolon(): void
    {
        $text = str_repeat('a', 351) . '; more text here.';

        $result = $this->extension->trimIntro($text);

        $this->assertStringEndsWith(';', $result);
    }

    public function testHtmlTagsAreStripped(): void
    {
        $text = '<p>Short <strong>text</strong> here.</p>';

        $result = $this->extension->trimIntro($text);

        $this->assertStringNotContainsString('<p>', $result);
        $this->assertStringNotContainsString('<strong>', $result);
        $this->assertEquals('Short text here.', $result);
    }

    public function testEmptyString(): void
    {
        $this->assertEquals('', $this->extension->trimIntro(''));
    }

    public function testTextWithOnlyHtmlTags(): void
    {
        $text = '<p><br><div></div></p>';

        $result = $this->extension->trimIntro($text);

        $this->assertEquals('', $result);
    }

    public function testLongTextWithoutPunctuation(): void
    {
        $text = str_repeat('a', 500);

        $result = $this->extension->trimIntro($text);

        $this->assertEquals($text, $result);
    }

    public function testLongHtmlTextIsTrimmedAfterStripping(): void
    {
        $text = '<p>' . str_repeat('a', 200) . '</p><p>' . str_repeat('b', 200) . '. More text here.</p>';

        $result = $this->extension->trimIntro($text);

        $this->assertStringEndsWith('.', $result);
        $this->assertStringNotContainsString('<p>', $result);
    }
}
