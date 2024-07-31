<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog;

use App\Criticalmass\CriticalmassBlog\Model\BlogArticle;
use Laminas\Feed\Reader\Reader;

class CriticalmassBlog implements CriticalmassBlogInterface
{
    public function getArticles(): array
    {
        try {
            $feed = Reader::import(static::BLOG_FEED_URL);
        } catch (\RuntimeException $exception) {
            return [];
        }

        $articleList = [];

        foreach ($feed as $entry) {
            $article = new BlogArticle($entry->getTitle(), $entry->getPermalink(), $entry->getDateCreated());

            $articleList[] = $article;
        }

        return $articleList;
    }
}
