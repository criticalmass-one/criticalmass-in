<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Criticalmass\TextParser\TextParserInterface;
use App\Twig\Extension\MarkdownTwigExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

final class MarkdownTwigExtensionTest extends TestCase
{
    #[Test]
    public function nullAndEmptyTextShortCircuitWithoutParsing(): void
    {
        $parser = $this->createMock(TextParserInterface::class);
        $parser->expects(self::never())->method('parse');

        $extension = new MarkdownTwigExtension($parser);

        self::assertSame('', $extension->markdown(null));
        self::assertSame('', $extension->markdown(''));
    }

    #[Test]
    public function textIsDelegatedToTheParser(): void
    {
        $parser = $this->createMock(TextParserInterface::class);
        $parser->expects(self::once())->method('parse')->with('**bold**')->willReturn('<p><strong>bold</strong></p>');

        self::assertSame('<p><strong>bold</strong></p>', (new MarkdownTwigExtension($parser))->markdown('**bold**'));
    }

    #[Test]
    public function registersHtmlSafeMarkdownFilter(): void
    {
        $filters = (new MarkdownTwigExtension($this->createMock(TextParserInterface::class)))->getFilters();

        self::assertCount(1, $filters);
        self::assertInstanceOf(TwigFilter::class, $filters[0]);
        self::assertSame('markdown', $filters[0]->getName());
        self::assertSame(['html'], $filters[0]->getSafe(new \Twig\Node\Expression\ConstantExpression('', 0)));
    }
}
