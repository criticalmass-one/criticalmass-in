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
                $ord = $this->changeChar(65, $ord);
            }

            if ($ord >= 97 && $ord <= 122) {
                $ord = $this->changeChar(97, $ord);
            }

            $text[$index] = chr($ord);
        }

        return $text;
    }

    protected function changeChar(int $range, int $ord, int $step = 1): int
    {
        return (($ord - $range + $step) % 26 + $range);
    }
}