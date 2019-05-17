<?php declare(strict_types=1);

namespace App\Criticalmass\Image\GoogleCloud\ExportDataHandler;

class Md5Base64Hasher
{
    private function __construct()
    {
    }

    public static function hash(string $content): string
    {
        return base64_encode(hex2bin(md5($content)));
    }
}