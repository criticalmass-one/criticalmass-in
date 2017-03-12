<?php

namespace Caldera\Bundle\CalderaBundle\Serializer\JMSSerializer\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

class CamelCaseNamingStrategy implements PropertyNamingStrategyInterface
{
    private $separator;
    private $lowerCase;

    public function __construct($separator = '_', $lowerCase = true)
    {
        $this->separator = $separator;
        $this->lowerCase = $lowerCase;
    }

    public function translateName(PropertyMetadata $property): string
    {
        $name = preg_replace('/[A-Z]/', $this->separator . '\\0', $property->name);

        if ($this->lowerCase) {
            return strtolower($name);
        }

        return lcfirst($name);
    }
}
