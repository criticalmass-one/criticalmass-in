<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Exception;

class ValidationException extends DataQueryException
{
    public function __construct(string $errorMessage)
    {
        $message = sprintf('Could not validate query: "%s"', $errorMessage);

        parent::__construct($message);
    }
}
