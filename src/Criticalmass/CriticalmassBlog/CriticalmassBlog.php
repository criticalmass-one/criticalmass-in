<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog;

use App\Criticalmass\CriticalmassBlog\Model\BlogArticle;
use Laminas\Feed\Reader\Reader;

class CriticalmassBlog implements CriticalmassBlogInterface
{
    public function getArticles(): array
    {
        $feed = Reader::import('https://criticalmass.blog/feed');

        $articleList = [];

        foreach ($feed as $entry) {
            $article = new BlogArticle($entry->getTitle(), $entry->getPermalink(), $entry->getDateCreated());

            $articleList[] = $article;
        }

        return $articleList;
    }
}