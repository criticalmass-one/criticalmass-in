<?php declare(strict_types=1);

namespace App\Criticalmass\Facebook\Bridge;

use App\Criticalmass\Facebook\Api\FacebookPageApi;

class CityBridge extends AbstractBridge
{
    /** @var FacebookPageApi $facebookPageApi */
    protected $facebookPageApi;

    public function __construct(FacebookPageApi $facebookPageApi)
    {
        $this->facebookPageApi = $facebookPageApi;
    }
}
