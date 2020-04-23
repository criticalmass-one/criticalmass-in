<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Exception;

class NotOneParameterForRequiredMethodException extends DataQueryException
{
    public function __construct(string $methodName, string $fqcn)
    {
        $message = sprintf('Method "%s" of class "%s" has no or more than one parameters', $methodName, $fqcn);

        parent::__construct($message);
    }
}