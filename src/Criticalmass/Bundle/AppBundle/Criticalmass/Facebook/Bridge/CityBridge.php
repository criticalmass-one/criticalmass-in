<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Facebook\Bridge;

use Criticalmass\Bundle\AppBundle\Criticalmass\Facebook\Api\FacebookPageApi;

class CityBridge extends AbstractBridge
{
    /** @var FacebookPageApi $facebookPageApi */
    protected $facebookPageApi;

    public function __construct(FacebookPageApi $facebookPageApi)
    {
        $this->facebookPageApi = $facebookPageApi;
    }
}
