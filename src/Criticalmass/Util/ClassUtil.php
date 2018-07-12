<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Util;

class ClassUtil
{
    public static function getShortname($object): string
    {
        $reflectionClass = new \ReflectionClass($object);

        return $reflectionClass->getShortName();
    }

    public static function getLowercaseShortname($object): string
    {
        return strtolower(self::getShortname($object));
    }
}
