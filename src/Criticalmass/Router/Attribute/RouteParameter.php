<?php declare(strict_types=1);

namespace App\Criticalmass\Router\Attribute;

#[\Attribute]
class RouteParameter implements AttributeInterface
{
    public function __construct(
        private ?string $name = null,
        private ?string $dateFormat = null
    )
    {

    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }
}
