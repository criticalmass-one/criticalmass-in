<?php

namespace Caldera\Bundle\CalderaBundle\Parser\MultiStepParser\Step;

use Caldera\Bundle\CalderaBundle\Parser\MultiStepParser\StepInterface;

class AnchorParserStep implements StepInterface
{
    public function __construct()
    {

    }

    protected static function callback(array $matches): string
    {
        $caption = $matches[1];

        $anchor = preg_replace("/[^A-Za-z0-9 ]/", '', $caption);
        $anchor = str_replace(' ', '-', $anchor);
        $anchor = urlencode($anchor);

        $result = '<h3><a class="anchor" name="' . $anchor . '" href="#' . $anchor . '">' . $caption . '</a></h3>';

        return $result;
    }

    public function parse(string $text): string
    {
        $text = preg_replace_callback('#\<h3\>(.*)\<\/h3\>#', 'self::callback', $text);

        return $text;
    }
}