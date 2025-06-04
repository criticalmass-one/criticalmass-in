<?php declare(strict_types=1);

namespace App\Criticalmass\SeoPage;

use App\Criticalmass\Router\ObjectRouterInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sonata\SeoBundle\Seo\SeoPageInterface as SonataSeoPageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractSeoPage implements SeoPageInterface
{
    public function __construct(
        protected readonly SonataSeoPageInterface $sonataSeoPage,
        protected readonly UploaderHelper $uploaderHelper,
        protected readonly CacheManager $cacheManager,
        protected readonly ObjectRouterInterface $objectRouter,
    ) {

    }
}
