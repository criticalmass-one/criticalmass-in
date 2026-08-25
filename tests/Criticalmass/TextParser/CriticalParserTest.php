<?php declare(strict_types=1);

namespace Tests\Criticalmass\TextParser;

use App\Criticalmass\TextParser\CriticalParser;
use App\Criticalmass\TextParser\TextCache\TextCacheInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CriticalParserTest extends TestCase
{
    /** @var array<string, string> */
    private array $cache = [];

    private function parser(): CriticalParser
    {
        $textCache = new class($this->cache) implements TextCacheInterface {
            /** @param array<string, string> $store */
            public function __construct(private array &$store)
            {
            }

            public function has(string $rawText): bool
            {
                return array_key_exists($rawText, $this->store);
            }

            public function get(string $rawText): string
            {
                return $this->store[$rawText];
            }

            public function set(string $rawText, string $parsedText): TextCacheInterface
            {
                $this->store[$rawText] = $parsedText;

                return $this;
            }
        };

        return new CriticalParser($textCache);
    }

    #[Test]
    public function convertsMarkdownToHtml(): void
    {
        self::assertSame("<p><strong>bold</strong> and <em>italic</em></p>\n", $this->parser()->parse('**bold** and *italic*'));
    }

    #[Test]
    public function rawHtmlIsStripped(): void
    {
        $html = $this->parser()->parse('hello <script>alert(1)</script> world');

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('hello', $html);
        self::assertStringContainsString('world', $html);
    }

    #[Test]
    public function unsafeLinksLoseTheirHref(): void
    {
        $html = $this->parser()->parse('[click](javascript:alert(1))');

        self::assertStringNotContainsString('javascript:', $html);
        self::assertStringContainsString('<a>click</a>', $html);
    }

    #[Test]
    public function explicitLinksAreKept(): void
    {
        self::assertSame(
            "<p><a href=\"https://criticalmass.in/\">CM</a></p>\n",
            $this->parser()->parse('[CM](https://criticalmass.in/)')
        );
    }

    #[Test]
    public function parsedTextIsCached(): void
    {
        $parser = $this->parser();

        $parser->parse('# Title');

        self::assertSame(["# Title" => "<h1>Title</h1>\n"], $this->cache);
    }

    #[Test]
    public function cachedTextIsReturnedWithoutReparsing(): void
    {
        $this->cache['# Title'] = '<p>from cache</p>';

        self::assertSame('<p>from cache</p>', $this->parser()->parse('# Title'));
    }

    #[Test]
    public function bareUrlsAreAutolinked(): void
    {
        $html = $this->parser()->parse('see https://criticalmass.in/hamburg now');

        self::assertSame("<p>see <a href=\"https://criticalmass.in/hamburg\">https://criticalmass.in/hamburg</a> now</p>\n", $html);
    }
}
