<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog;

interface CriticalmassBlogInterface
{
    public const BLOG_FEED_URL = 'https://criticalmass.blog/feed';

    public function getArticles(): array;
}
