<?php declare(strict_types=1);

namespace App\Criticalmass\Router\Attribute;

#[\Attribute]
class DefaultRoute implements AttributeInterface
{
    public function __construct(
        private ?string $name = null
    )
    {

    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
