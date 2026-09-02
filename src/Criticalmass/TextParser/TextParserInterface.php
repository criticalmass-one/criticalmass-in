<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

interface TextParserInterface
{
    public function parse(string $text): string;

    /**
     * Wie parse(), aber ohne das Ergebnis abzulegen — für Vorschauen, deren Zwischenstände
     * niemand ein zweites Mal braucht.
     */
    public function parseWithoutCache(string $text): string;
}