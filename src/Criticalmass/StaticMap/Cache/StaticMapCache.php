<?php declare(strict_types=1);

namespace App\Criticalmass\StaticMap\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class StaticMapCache
{
    private const string CACHE_NAMESPACE = 'criticalmass_staticmap';
    private const int DEFAULT_TTL = 604800; // 7 days

    private FilesystemAdapter $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter(
            self::CACHE_NAMESPACE,
            self::DEFAULT_TTL,
        );
    }

    public function get(string $cacheKey, callable $callback): ?string
    {
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($callback) {
            $item->expiresAfter(self::DEFAULT_TTL);

            return $callback();
        });
    }

    public static function generateCacheKey(array $parameters): string
    {
        return md5(serialize($parameters));
    }
}
