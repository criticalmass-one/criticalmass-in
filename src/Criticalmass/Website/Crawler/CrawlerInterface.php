<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Crawler;

interface CrawlerInterface
{
    public function crawlUrls(Crawlable $crawlable): array;
}
