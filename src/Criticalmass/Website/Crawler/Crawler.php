<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Crawler;

use Doctrine\Persistence\ManagerRegistry;

class Crawler implements CrawlerInterface
{
    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function crawlUrls(Crawlable $crawlable): array
    {
        $urlList = [];

        preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $crawlable->getText(), $resultList, PREG_PATTERN_ORDER);

        foreach ($resultList as $result) {
            $url = array_pop($result);

            if ($url) {
                $urlList[] = $url;
            }
        }

        return $urlList;
    }
}
