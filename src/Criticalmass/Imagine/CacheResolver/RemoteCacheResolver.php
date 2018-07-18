<?php declare(strict_types=1);

namespace App\Criticalmass\Imagine\CacheResolver;

use Liip\ImagineBundle\Imagine\Cache\Resolver\WebPathResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RequestContext;

class RemoteCacheResolver extends WebPathResolver
{
    public function __construct(
        Filesystem $filesystem,
        RequestContext $requestContext,
        string $webRootDir,
        string $cachePrefix = 'media/cache',
        LoggerInterface $logger
    ) {
        parent::__construct($filesystem, $requestContext, $webRootDir, $cachePrefix);
    }

    /**
     * @param string $path
     * @param string $filter
     */
    protected function getFileUrl($path, $filter): string
    {
        // crude way of sanitizing URL scheme ("protocol") part
        $path = str_replace(['https://', 'http://'], '', $path);
        $path = str_replace(['/', '.'], '_', $path);

        return sprintf('%s/%s/%s', $this->cachePrefix, $filter, trim($path, '/'));
    }
}
