<?php

namespace Criticalmass\Bundle\AppBundle\Twig\Extension;

use Criticalmass\Bundle\AppBundle\Criticalmass\Markdown\CriticalMarkdown;

class MarkdownTwigExtension extends \Twig_Extension
{
    protected $markdownParser;

    public function __construct(CriticalMarkdown $criticalMarkdown)
    {
        $this->markdownParser = $criticalMarkdown;
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('markdown', [$this, 'markdown'], array(
                'is_safe' => array('html'),
            )),
        ];
    }

    public function markdown(string $text): string
    {
        return $this->markdownParser->parse($text);
    }

    public function getName(): string
    {
        return 'markdown_extension';
    }
}

