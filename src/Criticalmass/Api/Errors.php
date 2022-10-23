<?php declare(strict_types=1);

namespace App\Criticalmass\Api;

use JMS\Serializer\Annotation as JMS;

class Errors
{
    /**
     * @JMS\Expose
     */
    protected int $httpStatusCode;

    /**
     * @JMS\Expose
     */
    protected array $errorMessageList;

    public function __construct(int $httpStatusCode, array $errorMessageList)
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->errorMessageList = $errorMessageList;
    }
}