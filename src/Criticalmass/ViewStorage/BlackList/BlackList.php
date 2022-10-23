<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\BlackList;

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class BlackList implements BlackListInterface
{
    public function isBlackListed(string $userAgent = null): bool
    {
        return (new CrawlerDetect())->isCrawler($userAgent);
    }
}
