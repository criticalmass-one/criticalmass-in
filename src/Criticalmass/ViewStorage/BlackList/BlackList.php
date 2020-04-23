<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\BlackList;

use Nmure\CrawlerDetectBundle\CrawlerDetect\CrawlerDetect;

class BlackList implements BlackListInterface
{
    /** @var CrawlerDetect $crawlerDetect */
    protected $crawlerDetect;

    public function __construct(CrawlerDetect $crawlerDetect)
    {
        $this->crawlerDetect = $crawlerDetect;
    }

    public function isBlackListed(string $userAgent = null): bool
    {
        return $this->crawlerDetect->isCrawler($userAgent);
    }
}
