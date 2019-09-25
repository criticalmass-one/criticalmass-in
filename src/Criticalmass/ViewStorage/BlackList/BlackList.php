<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\BlackList;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BlackList implements BlackListInterface
{
    /** @var Request $request */
    protected $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMasterRequest();
    }

    public function isBlackListed(): bool
    {
        $hostname = $this->request->getHost();

        return strpos($hostname, 'uptimerobot.com') !== false;
    }
}
