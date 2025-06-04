<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\LinkCache;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class LinkCache implements LinkCacheInterface
{
    const CACHE_NAMESPACE = 'caldera_criticalmass_linkcache';
    const DEFAULT_TTL = 86400;

    protected AdapterInterface $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter(
            self::CACHE_NAMESPACE,
            self::DEFAULT_TTL,
        );
    }

    protected function hashLink(string $link): string
    {
        return md5($link);
    }

    public function has(string $link): bool
    {
        return  $this->cache->hasItem($this->hashLink($link));
    }

    public function get(string $link): string
    {
        $item = $this->cache->getItem($this->hashLink($link));

        return $item->get();
    }

    public function set(string $link, string $html): self
    {
        $item = $this->cache->getItem($this->hashLink($link));
        $item->set($html);
        $this->cache->save($item);

        return $this;
    }
}