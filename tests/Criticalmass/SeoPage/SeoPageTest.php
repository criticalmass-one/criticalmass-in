<?php declare(strict_types=1);

namespace Tests\Criticalmass\SeoPage;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\SeoPage\SeoPage;
use App\Entity\City;
use App\Entity\Photo;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\SeoBundle\Seo\SeoPage as SonataSeoPage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class SeoPageTest extends TestCase
{
    private SonataSeoPage $sonataPage;
    private MockObject&StorageInterface $storage;
    private MockObject&CacheManager $cacheManager;
    private MockObject&ObjectRouterInterface $objectRouter;
    private SeoPage $seoPage;

    protected function setUp(): void
    {
        $this->sonataPage = new SonataSeoPage('criticalmass.in');
        $this->storage = $this->createMock(StorageInterface::class);
        $this->cacheManager = $this->createMock(CacheManager::class);
        $this->objectRouter = $this->createMock(ObjectRouterInterface::class);

        $this->seoPage = new SeoPage($this->sonataPage, new UploaderHelper($this->storage), $this->cacheManager, $this->objectRouter);
    }

    #[Test]
    public function titleIsMirroredIntoOpenGraph(): void
    {
        $this->seoPage->setTitle('Critical Mass Hamburg');

        self::assertSame('Critical Mass Hamburg', $this->sonataPage->getTitle());
        self::assertSame('Critical Mass Hamburg', $this->sonataPage->getMetas()['property']['og:title'][0]);
    }

    #[Test]
    public function descriptionFeedsBothMetaAndOpenGraph(): void
    {
        $this->seoPage->setDescription('Monthly ride');

        $metas = $this->sonataPage->getMetas();
        self::assertSame('Monthly ride', $metas['name']['description'][0]);
        self::assertSame('Monthly ride', $metas['property']['og:description'][0]);
    }

    #[Test]
    public function canonicalLinkAlsoSetsOgUrl(): void
    {
        $this->seoPage->setCanonicalLink('https://criticalmass.in/hamburg');

        self::assertSame('https://criticalmass.in/hamburg', $this->sonataPage->getLinkCanonical());
        self::assertSame('https://criticalmass.in/hamburg', $this->sonataPage->getMetas()['property']['og:url'][0]);
    }

    #[Test]
    public function canonicalForObjectUsesAbsoluteObjectUrl(): void
    {
        $city = new City();
        $this->objectRouter->expects(self::once())
            ->method('generate')
            ->with($city, null, [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://criticalmass.in/hamburg');

        $this->seoPage->setCanonicalForObject($city);

        self::assertSame('https://criticalmass.in/hamburg', $this->sonataPage->getLinkCanonical());
    }

    #[Test]
    public function previewPhotoRegistersFacebookAndTwitterImages(): void
    {
        $photo = (new Photo())->setImageName('ride.jpg');

        $this->storage->method('resolveUri')->with($photo, 'imageFile')->willReturn('/uploads/ride.jpg');
        $this->cacheManager->method('getBrowserPath')->willReturnCallback(
            static fn (string $path, string $filter): string => sprintf('https://cdn/%s%s', $filter, $path)
        );

        $this->seoPage->setPreviewPhoto($photo);

        $metas = $this->sonataPage->getMetas();
        self::assertSame('https://cdn/facebook_preview_image/uploads/ride.jpg', $metas['property']['og:image'][0]);
        self::assertSame('https://cdn/twitter_summary_large_image/uploads/ride.jpg', $metas['name']['twitter:image'][0]);
        self::assertSame('summary_large_image', $metas['name']['twitter:card'][0]);
    }

    #[Test]
    public function previewPhotoWithoutImageIsIgnored(): void
    {
        $this->cacheManager->expects(self::never())->method('getBrowserPath');

        $this->seoPage->setPreviewPhoto(new Photo());

        self::assertArrayNotHasKey('og:image', $this->sonataPage->getMetas()['property'] ?? []);
    }

    #[Test]
    public function settersAreFluent(): void
    {
        self::assertSame($this->seoPage, $this->seoPage->setTitle('t'));
        self::assertSame($this->seoPage, $this->seoPage->setDescription('d'));
        self::assertSame($this->seoPage, $this->seoPage->setCanonicalLink('l'));
    }
}
