<?php

namespace Caldera\Bundle\CalderaBundle\Parser;

interface ParserInterface
{
    public function parse(string $text): string;
}