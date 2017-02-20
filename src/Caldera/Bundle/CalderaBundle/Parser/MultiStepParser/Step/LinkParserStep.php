<?php

namespace Caldera\Bundle\CalderaBundle\Parser\MultiStepParser\Step;

use Caldera\Bundle\CalderaBundle\Parser\MultiStepParser\StepInterface;

/**
 * Class LinkParserStep
 *
 * Regex stolen from: http://zenverse.net/php-function-to-auto-convert-url-into-hyperlink/
 *
 * @package Caldera\Bundle\CalderaBundle\Parser\MultiStepParser\Step
 */
class LinkParserStep implements StepInterface
{
    public function __construct()
    {

    }

    protected static function callback(array $matches): string
    {
        $ret = '';
        $url = $matches[2];

        if (empty($url))
            return $matches[0];
        // removed trailing [.,;:] from URL
        if (in_array(substr($url, -1), array('.', ',', ';', ':')) === true) {
            $ret = substr($url, -1);
            $url = substr($url, 0, strlen($url) - 1);
        }

        return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
    }

    public function parse(string $ret): string
    {
        $ret = ' ' . $ret;
        // in testing, using arrays here was found to be faster
        $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'self::callback', $ret);

        // this one is not in an array because we need it to run last, for cleanup of accidental links within links
        $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
        $ret = trim($ret);
        return $ret;
    }
}