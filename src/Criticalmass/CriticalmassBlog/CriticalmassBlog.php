<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog;

use App\Criticalmass\CriticalmassBlog\Model\BlogArticle;

class CriticalmassBlog implements CriticalmassBlogInterface
{
    public function getArticles(): array
    {
        try {
            $xml = @simplexml_load_file(static::BLOG_FEED_URL, 'SimpleXMLElement', LIBXML_NOCDATA);

            if (!$xml || !isset($xml->channel->item)) {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }

        $articleList = [];

        foreach ($xml->channel->item as $entry) {
            $title = (string) $entry->title;
            $link = (string) $entry->link;
            $pubDate = new \DateTime((string) $entry->pubDate);

            $articleList[] = new BlogArticle($title, $link, $pubDate);
        }

        return $articleList;
    }
}
