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
    /** @var SonataSeoPageInterface $sonataSeoPage*/
    protected $sonataSeoPage;

    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var CacheManager $cacheManager */
    protected $cacheManager;

    /** @var ObjectRouter $objectRouter */
    protected $objectRouter;

    /** @var UrlGeneratorInterface $urlGenerator */
    protected $urlGenerator;

    public function __construct(
        SonataSeoPageInterface $sonataSeoPage,
        UploaderHelper $uploaderHelper,
        CacheManager $cacheManager,
        ObjectRouterInterface $objectRouter,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->sonataSeoPage = $sonataSeoPage;
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
        $this->objectRouter = $objectRouter;
        $this->urlGenerator = $urlGenerator;
    }
}
