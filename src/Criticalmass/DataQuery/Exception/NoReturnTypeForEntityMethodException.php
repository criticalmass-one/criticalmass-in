<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Exception;

class NoReturnTypeForEntityMethodException extends DataQueryException
{
    public function __construct(string $methodName, string $fqcn)
    {
        $message = sprintf('Method "%s" of class "%s" has no return type', $methodName, $fqcn);

        parent::__construct($message);
    }
}