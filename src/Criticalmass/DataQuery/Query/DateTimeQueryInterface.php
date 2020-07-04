<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

interface DateTimeQueryInterface
{
    public function setDateTimePattern(string $dateTimePattern): DateTimeQueryInterface;

    public function setDateTimeFormat(string $dateTimeFormat): DateTimeQueryInterface;

    public function setPropertyName(string $propertyName): DateTimeQueryInterface;
}