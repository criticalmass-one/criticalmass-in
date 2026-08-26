<?php declare(strict_types=1);

namespace App\Criticalmass\SeoPage;

use App\Criticalmass\Router\ObjectRouterInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractSeoPage implements SeoPageInterface
{
    public function __construct(
        protected readonly PageMetadata $pageMetadata,
        protected readonly UploaderHelper $uploaderHelper,
        protected readonly CacheManager $cacheManager,
        protected readonly ObjectRouterInterface $objectRouter,
    ) {

    }
}
