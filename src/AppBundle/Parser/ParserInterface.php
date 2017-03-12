<?php

namespace AppBundle\Parser;

interface ParserInterface
{
    public function parse(string $text): string;
}