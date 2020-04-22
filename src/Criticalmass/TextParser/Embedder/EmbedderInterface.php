<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\Embedder;

interface EmbedderInterface
{
    public function processEmbedsInText(string $text): string;
}