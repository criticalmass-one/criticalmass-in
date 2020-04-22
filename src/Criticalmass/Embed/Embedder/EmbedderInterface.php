<?php declare(strict_types=1);

namespace App\Criticalmass\Embed\Embedder;

interface EmbedderInterface
{
    public function processEmbedsInText(string $text): string;
}