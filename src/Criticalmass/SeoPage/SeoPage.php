<?php declare(strict_types=1);

namespace App\Criticalmass\SeoPage;

use App\EntityInterface\PhotoInterface;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class SeoPage extends AbstractSeoPage
{
    public function setTitle(string $title): SeoPageInterface
    {
        $this->sonataSeoPage
            ->setTitle($title)
            ->addMeta('property', 'og:title', $title);

        return $this;
    }

    public function setDescription(string $description): SeoPageInterface
    {
        $this->sonataSeoPage
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:description', $description);

        return $this;
    }

    public function setPreviewPhoto(PhotoInterface $object): SeoPageInterface
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

    public function setCanonicalLink(string $link): SeoPageInterface
    {
        $this->sonataSeoPage
            ->setLinkCanonical($link)
            ->addMeta('property', 'og:url', $link);

        return $this;
    }

    public function setCanonicalForObject(RouteableInterface $object): SeoPageInterface
    {
        $url = $this->objectRouter->generate($object, null, [], SymfonyUrlGeneratorInterface::ABSOLUTE_URL);

        $this->setCanonicalLink($url);

        return $this;
    }
}
