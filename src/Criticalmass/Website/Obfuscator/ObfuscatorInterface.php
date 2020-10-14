<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Obfuscator;

interface ObfuscatorInterface
{
    public function obfuscate(string $text): string;
}