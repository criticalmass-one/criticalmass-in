<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Design\DesignChoice;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Ride;
use App\Enum\PostKindEnum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Tests\Controller\AbstractControllerTestCase;

class PhotosPostsTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['FEATURE_DESIGN_V2'] = 'true';
        $_ENV['FEATURE_STATUS_POSTS'] = 'true';
    }

    protected function tearDown(): void
    {
        unset($_ENV['FEATURE_DESIGN_V2'], $_ENV['FEATURE_STATUS_POSTS']);

        parent::tearDown();
    }

    private function createV2Client(?string $email = null): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));

        if (null !== $email) {
            $this->loginAs($client, $email);
        }

        return $client;
    }

    private function rideWithPhotosOf(string $email): Ride
    {
        $photo = static::getContainer()->get('doctrine')->getRepository(Photo::class)->findOneBy([
            'user' => $this->getUser($email),
            'enabled' => true,
            'deleted' => false,
        ]);

        self::assertInstanceOf(Photo::class, $photo);
        self::assertInstanceOf(Ride::class, $photo->getRide());

        return $photo->getRide();
    }

    private function path(Ride $ride, string $routeName): string
    {
        return static::getContainer()->get(ObjectRouterInterface::class)->generate($ride, $routeName);
    }

    public function testExampleGallery(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/city/gallery');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-page-head h1', 'Fotos');
        self::assertSelectorTextContains('.cm-sidenav a.is-active', 'Fotos');
        self::assertSelectorExists('.cm-pp-masonry figure img');
    }

    public function testManageAndUploadPages(): void
    {
        $client = $this->createV2Client('testuser@criticalmass.in');
        $ride = $this->rideWithPhotosOf('testuser@criticalmass.in');

        $client->request('GET', $this->path($ride, 'caldera_criticalmass_photo_manage'));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('.cm-pp-manage figure .dropdown-menu a[href$="/toggle"]');
        self::assertSelectorExists('.cm-pp-manage a[href*="/rotate?direction=left"]');

        $client->request('GET', $this->path($ride, 'caldera_criticalmass_gallery_photos_upload_ride'));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorNotExists('[data-controller="unified-upload"]');
        self::assertSelectorExists('a[href$="/addphoto-legacy"]');

        $client->request('GET', $this->path($ride, 'caldera_criticalmass_gallery_legacy_photos_upload_ride'));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists('form[enctype="multipart/form-data"] input[type="file"]');
    }

    public function testFailedCommentShowsTheFormAgain(): void
    {
        $client = $this->createV2Client('testuser@criticalmass.in');
        $ride = $this->rideWithPhotosOf('testuser@criticalmass.in');

        $crawler = $client->request('GET', '/post/write/ride/' . $ride->getId());
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Speichern')->form();
        $form['post[text]'] = '';
        $client->submit($form);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-forum-head h1', 'Kommentar schreiben');
        self::assertSelectorExists('.cm-pp-error');
        self::assertSelectorExists('#form-post textarea');
    }

    public function testStatusFeedAndUserPage(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/beitraege');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-page-head h1', 'Beiträge');
        self::assertSelectorTextContains('.cm-sidenav a.is-active', 'Beiträge');

        $client->request('GET', '/beitraege/cyclist');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorTextContains('.cm-forum-profile h1', 'cyclist');
        self::assertSelectorExists('.cm-forum-profile a[href="/forum/user/cyclist"]');
    }

    public function testReportFormAndModerationList(): void
    {
        $client = $this->createV2Client();
        $em = static::getContainer()->get('doctrine')->getManager();

        $city = $em->getRepository(City::class)->findOneBy([]);
        self::assertInstanceOf(City::class, $city);

        $post = (new Post())
            ->setUser($this->getUser('cyclist@criticalmass.in'))
            ->setCity($city)
            ->setKind(PostKindEnum::STATUS)
            ->setText('Beitrag zum Melden in der neuen Ansicht')
            ->setEnabled(true);
        $em->persist($post);
        $em->flush();

        try {
            $client->request('GET', '/post/' . $post->getId() . '/report');
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('body.cm-v2');
            self::assertSelectorTextContains('.cm-pp-quote', 'Beitrag zum Melden');
            self::assertSelectorExists('#form-post-report input[type="radio"]');
            self::assertSelectorExists('#form-post-report input[type="email"]');
            self::assertSelectorExists('.frc-captcha');

            $this->loginAs($client, 'admin@criticalmass.in');
            $client->request('GET', '/moderation/reports');
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('body.cm-v2');
            self::assertSelectorTextContains('.cm-forum-head h1', 'Gemeldete Beiträge');
        } finally {
            $em->remove($em->getRepository(Post::class)->find($post->getId()));
            $em->flush();
        }
    }
}
