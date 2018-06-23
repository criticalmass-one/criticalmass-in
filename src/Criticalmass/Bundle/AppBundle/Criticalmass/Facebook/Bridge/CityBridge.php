<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook\Bridge;

use Criticalmass\Component\Facebook\Api\FacebookPageApi;

class CityBridge extends AbstractBridge
{
    /** @var FacebookPageApi $facebookPageApi */
    protected $facebookPageApi;

    public function __construct(FacebookPageApi $facebookPageApi)
    {
        $this->facebookPageApi = $facebookPageApi;
    }
}
