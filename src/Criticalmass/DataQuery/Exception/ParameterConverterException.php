<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Exception;

class ParameterConverterException extends DataQueryException
{
    public function __construct(string $expectedType, string $stringValue, string $parameterName = null)
    {
        if ($parameterName) {
            $message = sprintf('Could not convert value "%s" into type "%s" for parameter "%s"', $stringValue, $expectedType, $parameterName);
        } else {
            $message = sprintf('Could not convert value "%s" into type "%s"', $stringValue, $expectedType);
        }

        parent::__construct($message);
    }
}
