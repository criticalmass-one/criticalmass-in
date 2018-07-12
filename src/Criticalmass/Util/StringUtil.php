<?php declare(strict_types=1);

namespace App\Criticalmass\Util;

class StringUtil
{
    public static function camelCaseToUnderscore(string $input, bool $avoidDoubleUnderscore = true): string
    {
        $output = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));

        if ($avoidDoubleUnderscore) {
            $output = str_replace('__', '_', $output);
        }

        return $output;
    }
}
