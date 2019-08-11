<?php declare(strict_types=1);

namespace App\Twig\Extension;

use League\CommonMark\CommonMarkConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    public function markdown(string $text): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convertToHtml($text);
    }

    public function getName(): string
    {
        return 'markdown_extension';
    }
}
