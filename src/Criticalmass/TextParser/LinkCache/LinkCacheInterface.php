<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\LinkCache;

interface LinkCacheInterface
{
    public function has(string $link): bool;

    public function get(string $link): string;

    public function set(string $link, string $html): self;
}