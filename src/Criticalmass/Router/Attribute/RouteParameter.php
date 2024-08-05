<?php declare(strict_types=1);

namespace App\Criticalmass\Router\Attribute;

use Attribute;

#[Attribute]
class RouteParameter
{
    public function __construct(
        private ?string $name = null,
        private ?string $dateFormat = null
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }
}
