<?php

namespace Caldera\Bundle\CalderaBundle\Parser\MultiStepParser\Step;

use Caldera\Bundle\CalderaBundle\Parser\MultiStepParser\StepInterface;

class MarkdownParserStep implements StepInterface
{
    protected $markdownParser;

    public function __construct($markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse(string $text): string
    {
        return $this->markdownParser->transform($text);
    }
}