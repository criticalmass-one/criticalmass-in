<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\TextCache;

interface TextCacheInterface
{
    public function has(string $rawText): bool;

    public function get(string $rawText): string;

    public function set(string $rawText, string $parsedText): self;
}