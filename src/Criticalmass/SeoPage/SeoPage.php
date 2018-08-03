<?php declare(strict_types=1);

namespace App\Criticalmass\SeoPage;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\StaticMap\UrlGenerator\UrlGeneratorInterface;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\RouteableInterface;
use App\Criticalmass\Router\ObjectRouter;
use App\EntityInterface\StaticMapableInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SeoPage
{
    /** @var SeoPageInterface */
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
        SeoPageInterface $sonataSeoPage,
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

    public function setTitle(string $title): SeoPage
    {
        $this->sonataSeoPage
            ->setTitle($title)
            ->addMeta('property', 'og:title', $title);

        return $this;
    }

    public function setDescription(string $description): SeoPage
    {
        $this->sonataSeoPage
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:description', $description);

        return $this;
    }

    public function setPreviewMap(StaticMapableInterface $staticMapable): SeoPage
    {
        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $this->urlGenerator->generate($staticMapable, 600, 315))
            ->addMeta('name', 'twitter:image', $this->urlGenerator->generate($staticMapable, 800, 320))
            ->addMeta('name', 'twitter:card', 'summary_large_image');

        return $this;
    }

    public function setPreviewPhoto(PhotoInterface $object): SeoPage
    {
        if (!$object->getImageName()) {
            return $this;
        }

        $imageFilename = $this->uploaderHelper->asset($object, 'imageFile');

        $facebookPreviewPath = $this->cacheManager->getBrowserPath($imageFilename, 'facebook_preview_image');
        $twitterPreviewPath = $this->cacheManager->getBrowserPath($imageFilename, 'twitter_summary_large_image');

        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $facebookPreviewPath)
            ->addMeta('name', 'twitter:image', $twitterPreviewPath)
            ->addMeta('name', 'twitter:card', 'summary_large_image');

        return $this;
    }

    public function setCanonicalLink(string $link): SeoPage
    {
        $this->sonataSeoPage
            ->setLinkCanonical($link)
            ->addMeta('property', 'og:url', $link);

        return $this;
    }

    public function setCanonicalForObject(RouteableInterface $object): SeoPage
    {
        $url = $this->objectRouter->generate($object, null, [], SymfonyUrlGeneratorInterface::ABSOLUTE_URL);

        $this->setCanonicalLink($url);

        return $this;
    }
}
