<?php declare(strict_types=1);

namespace App\Criticalmass\SeoPage;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\StaticMap\UrlGenerator\UrlGeneratorInterface;
use App\Criticalmass\Router\ObjectRouter;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sonata\SeoBundle\Seo\SeoPageInterface as SonataSeoPageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractSeoPage implements SeoPageInterface
{
    public function __construct(protected SonataSeoPageInterface $sonataSeoPage, protected UploaderHelper $uploaderHelper, protected CacheManager $cacheManager, protected ObjectRouterInterface $objectRouter, protected UrlGeneratorInterface $urlGenerator)
    {
    }
}
