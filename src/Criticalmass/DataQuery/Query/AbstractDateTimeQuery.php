<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Symfony\Component\Validator\Constraints as Constraints;

abstract class AbstractDateTimeQuery extends AbstractQuery implements DateTimeQueryInterface
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Type("string")
     * @var string $dateTimePattern
     */
    protected $dateTimePattern;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("string")
     * @var string $dateTimeFormat
     */
    protected $dateTimeFormat;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("string")
     * @var string $propertyName
     */
    protected $propertyName;

    public function setDateTimePattern(string $dateTimePattern): DateTimeQueryInterface
    {
        $this->dateTimePattern = $dateTimePattern;

        return $this;
    }

    public function setDateTimeFormat(string $dateTimeFormat): DateTimeQueryInterface
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    public function setPropertyName(string $propertyName): DateTimeQueryInterface
    {
        $this->propertyName = $propertyName;

        return $this;
    }
}
