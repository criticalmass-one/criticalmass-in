<?php declare(strict_types=1);

namespace App\Criticalmass\Api;

class Errors
{
    public function __construct(
        private readonly int $httpStatusCode,
        private readonly array $errorMessageList
    )
    {

    }
}
