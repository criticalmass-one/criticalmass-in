<?php declare(strict_types=1);

namespace App\Criticalmass\Api;

use JMS\Serializer\Annotation as JMS;

/** @deprecated */
class Error
{
    /**
     * @JMS\Expose
     */
    protected int $httpStatusCode;

    /**
     * @JMS\Expose
     */
    protected string $errorMessage;

    public function __construct(int $httpStatusCode, string $errorMessage)
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->errorMessage = $errorMessage;
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