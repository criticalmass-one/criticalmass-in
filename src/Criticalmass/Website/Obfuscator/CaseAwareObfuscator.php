<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Obfuscator;

class CaseAwareObfuscator implements ObfuscatorInterface
{
    public function obfuscate(string $text): string
    {
        $length = strlen($text);

        for ($index = 0; $index < $length; ++$index) {
            $ord = ord($text[$index]);

            if ($ord >= 65 && $ord <= 90) {
                $ord += 1;

                if ($ord > 90) {
                    $ord -= 26;
                }
            }

            if ($ord >= 97 && $ord <= 122) {
                $ord += 1;

                if ($ord > 122) {
                    $ord -= 26;
                }
            }

            $text[$index] = chr($ord);
        }

        return $text;
    }
}