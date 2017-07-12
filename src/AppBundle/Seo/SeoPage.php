<?php

namespace AppBundle\Seo;

use AppBundle\EntityInterface\PhotoInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SeoPage
{
    /** @var SeoPageInterface */
    protected $sonataSeoPage;

    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var CacheManager $cacheManager */
    protected $cacheManager;

    public function __construct(SeoPageInterface $sonataSeoPage, UploaderHelper $uploaderHelper, CacheManager $cacheManager)
    {
        $this->sonataSeoPage = $sonataSeoPage;
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
    }

    public function setTitle(string $title): SeoPage
    {
        $this->sonataSeoPage
            ->setTitle($title)
            ->addMeta('property', 'og:title', $title)
        ;

        return $this;
    }

    public function setDescription(string $description): SeoPage
    {
        $this->sonataSeoPage
            ->addMeta('name', 'description',$description)
            ->addMeta('property', 'og:description', $description)
        ;

        return $this;
    }

    public function setPreviewPhoto(PhotoInterface $object): SeoPage
    {
        $imageFilename = $this->uploaderHelper->asset($object, 'imageFile');

        $facebookPreviewPath = $this->cacheManager->getBrowserPath($imageFilename, 'facebook_preview_image');
        $twitterPreviewPath = $this->cacheManager->getBrowserPath($imageFilename, 'twitter_summary_large_image');

        $this->sonataSeoPage
            ->addMeta('property', 'og:image', $facebookPreviewPath)
            ->addMeta('name', 'twitter:image', $twitterPreviewPath)
        ;

        return $this;
    }
}