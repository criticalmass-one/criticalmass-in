<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\TextParser\TextParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownTwigExtension extends AbstractExtension
{
    protected TextParserInterface $textParser;

    public function __construct(TextParserInterface $textParser)
    {
        $this->textParser = $textParser;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    public function markdown(string $text): string
    {
        return $this->textParser->parse($text);
    }

    public function getName(): string
    {
        return 'markdown_extension';
    }
}
