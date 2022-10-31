<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\TextCache;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class TextCache implements TextCacheInterface
{
    final const CACHE_NAMESPACE = 'caldera_criticalmass_textcache';
    final const DEFAULT_TTL = 604800;

    protected AdapterInterface $cache;

    public function __construct(string $redisUrl)
    {
        $this->cache = new RedisAdapter(
            RedisAdapter::createConnection($redisUrl),
            self::CACHE_NAMESPACE,
            self::DEFAULT_TTL,
        );
    }

    protected function hashText(string $rawText): string
    {
        return md5($rawText);
    }

    public function has(string $rawText): bool
    {
        return $this->cache->hasItem($this->hashText($rawText));
    }

    public function get(string $rawText): string
    {
        $item = $this->cache->getItem($this->hashText($rawText));

        return $item->get();
    }

    public function set(string $rawText, string $parsedText): self
    {
        $item = $this->cache->getItem($this->hashText($rawText));
        $item->set($parsedText);
        $this->cache->save($item);

        return $this;
    }
}