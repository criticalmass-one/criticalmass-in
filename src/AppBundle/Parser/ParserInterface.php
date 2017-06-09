<?php

namespace AppBundle\Parser;

/**
 * @deprecated
 */
interface ParserInterface
{
    public function parse(string $text): string;
}