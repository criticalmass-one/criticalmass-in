<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Parser;

use App\Entity\CrawledWebsite;

interface ParserInterface
{
    public function parse(string $url): ?CrawledWebsite;
}
