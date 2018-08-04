<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Crawler;

interface Crawlable
{
    public function isCrawled(): bool;
    public function setCrawled(bool $crawled): Crawlable;
    public function getText(): string;
}
