<?php declare(strict_types=1);

namespace App\Criticalmass\Api;

use JMS\Serializer\Annotation as JMS;

class Errors
{
    public function __construct(
        /**
         * @JMS\Expose
         */
        protected int $httpStatusCode,
        /**
         * @JMS\Expose
         */
        protected array $errorMessageList
    )
    {
    }
}
