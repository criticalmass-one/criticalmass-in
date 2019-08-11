<?php

namespace App\Twig\Extension;

use League\CommonMark\CommonMarkConverter;

class MarkdownTwigExtension extends \Twig_Extension
{
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
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
