<?php declare(strict_types=1);

namespace App\Criticalmass\Util;

class ClassUtil
{
    public static function getShortnameFromFqcn(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);

        return array_pop($parts);
    }

    public static function getLowercaseShortnameFromFqcn(string $fqcn): string
    {
        return strtolower(self::getShortnameFromFqcn($fqcn));
    }

    public static function getShortname($object): string
    {
        $reflectionClass = new \ReflectionClass($object);

        return $reflectionClass->getShortName();
    }

    public static function getLowercaseShortname($object): string
    {
        return strtolower(self::getShortname($object));
    }

    public static function getLcfirstShortname($object): string
    {
        return lcfirst(self::getShortname($object));
    }
}
