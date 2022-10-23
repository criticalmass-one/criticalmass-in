<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\BlackList;

use Nmure\CrawlerDetectBundle\CrawlerDetect\CrawlerDetect;

class BlackList implements BlackListInterface
{
    public function __construct(protected CrawlerDetect $crawlerDetect)
    {
    }

    public function isBlackListed(string $userAgent = null): bool
    {
        return $this->crawlerDetect->isCrawler($userAgent);
    }
}
