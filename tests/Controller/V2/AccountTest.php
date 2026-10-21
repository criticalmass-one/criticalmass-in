<?php declare(strict_types=1);

namespace Tests\Controller\V2;

use App\Design\DesignChoice;
use App\Entity\Track;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Tests\Controller\AbstractControllerTestCase;

class AccountTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['FEATURE_DESIGN_V2'] = 'true';
    }

    protected function tearDown(): void
    {
        unset($_ENV['FEATURE_DESIGN_V2']);

        parent::tearDown();
    }

    private function createV2Client(): KernelBrowser
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie(DesignChoice::COOKIE, DesignChoice::V2));
        $this->loginAs($client, 'cyclist@criticalmass.in');

        return $client;
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function pageProvider(): array
    {
        return [
            'Konto' => ['/profile/', '.cm-acc-who h1'],
            'Benutzername' => ['/profile/username', '.cm-acc-form input[name$="[username]"]'],
            'E-Mail-Adresse' => ['/profile/email', '.cm-acc-form input[name$="[email]"]'],
            'Profilfoto' => ['/profile/profilephoto', '.cm-acc-form input[type="file"]'],
            'Profilfarbe' => ['/profile/color', '.cm-acc-form input[type="color"]'],
            'Passkeys' => ['/profile/passkeys', '[data-action="passkey#register"]'],
            'Tracks' => ['/track/list', '.cm-page-head h1'],
            'Fotos' => ['/photos/list', '.cm-page-head h1'],
            'Hochladen' => ['/upload', '[data-controller="unified-upload"] [data-unified-upload-target="dashboard"]'],
            'Prüfen' => ['/upload/review', '.cm-acc-sec-head h2'],
        ];
    }

    #[DataProvider('pageProvider')]
    public function testPageRendersInNewDesign(string $url, string $selector): void
    {
        $client = $this->createV2Client();

        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('body.cm-v2');
        self::assertSelectorExists($selector);
    }

    public function testTrackListShowsMenuForOwner(): void
    {
        $client = $this->createV2Client();

        $em = static::getContainer()->get('doctrine')->getManager();
        $track = $em->getRepository(Track::class)->findOneBy(['username' => 'cyclist', 'enabled' => true, 'deleted' => false]);
        self::assertInstanceOf(Track::class, $track);

        $track->setReviewed(false);
        $em->flush();

        try {
            $client->request('GET', '/track/list');

            self::assertResponseIsSuccessful();
            self::assertSelectorExists('.cm-acc-menu .dropdown-menu a[href*="/track/range/"]');
            self::assertSelectorExists('.cm-acc-menu form[method="post"][action$="/approve"] input[name="_token"]');
        } finally {
            $track->setReviewed(true);
            $em->flush();
        }
    }

    public function testAccountPageKeepsDesignSwitch(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/profile/');

        self::assertSelectorExists('form[action="/ansicht"][data-controller="auto-submit"] input#design-v2[checked]');
        self::assertSelectorExists('a[href="/profile/passkeys"]');
        self::assertSelectorExists('a[href="/logout"]');
    }

    public function testUploadPageKeepsUploaderWiring(): void
    {
        $client = $this->createV2Client();

        $client->request('GET', '/upload');

        self::assertSelectorExists('[data-unified-upload-upload-url-value="/upload/file"][data-unified-upload-csrf-token-value]');
        self::assertSelectorExists('[data-unified-upload-target="results"]');
        self::assertSelectorExists('a[data-unified-upload-target="reviewLink"][href="/upload/review"]');
    }
}
