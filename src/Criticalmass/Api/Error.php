<?php declare(strict_types=1);

namespace App\Criticalmass\Api;

use JMS\Serializer\Annotation as JMS;

/** @deprecated */
class Error
{
    public function __construct(
        /**
         * @JMS\Expose
         */
        protected int $httpStatusCode,
        /**
         * @JMS\Expose
         */
        protected string $errorMessage
    )
    {
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}