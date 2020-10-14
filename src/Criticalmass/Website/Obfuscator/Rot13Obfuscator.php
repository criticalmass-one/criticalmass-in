<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Obfuscator;

class Rot13Obfuscator implements ObfuscatorInterface
{
    public function obfuscate(string $text): string
    {
        return str_rot13($text);
    }
}