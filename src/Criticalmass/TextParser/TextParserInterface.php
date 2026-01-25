<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

interface TextParserInterface
{
    public function parse(string $text): string;
}