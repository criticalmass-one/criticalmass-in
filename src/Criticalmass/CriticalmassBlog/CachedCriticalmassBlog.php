<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CachedCriticalmassBlog extends CriticalmassBlog
{
    const CACHE_NAMESPACE = 'caldera_criticalmass_blog_articles';
    const DEFAULT_TTL = 3600;
    const CACHE_KEY = 'blog_articles';

    protected AdapterInterface $cache;

    public function __construct(string $redisUrl)
    {
        $this->cache = new RedisAdapter(
            RedisAdapter::createConnection($redisUrl),
            static::CACHE_NAMESPACE,
            static::DEFAULT_TTL,
        );
    }

    public function getArticles(): array
    {
        $articleList = $this->cache->get(static::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(static::DEFAULT_TTL);

            return parent::getArticles();
        });

        return $articleList;
    }
}
