<?php

namespace AppBundle\Parser\MultiStepParser\Step;

use AppBundle\Parser\MultiStepParser\StepInterface;

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