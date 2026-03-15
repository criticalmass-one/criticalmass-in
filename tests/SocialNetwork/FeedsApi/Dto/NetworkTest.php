<?php declare(strict_types=1);

namespace Tests\SocialNetwork\FeedsApi\Dto;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox('FeedsApi Network DTO')]
class NetworkTest extends TestCase
{
    private function createTwitterNetwork(): Network
    {
        return Network::fromApiResponse([
            'id' => 108,
            'identifier' => 'twitter',
            'name' => 'Twitter',
            'icon' => 'fab fa-twitter',
            'backgroundColor' => 'rgb(29, 161, 242)',
            'textColor' => 'white',
            'profileUrlPattern' => '#^https?://(www\.)?(twitter|x)\.com/[A-Za-z0-9_]+/?$#i',
        ]);
    }

    #[TestDox('implements NetworkInterface')]
    public function testImplementsNetworkInterface(): void
    {
        $network = $this->createTwitterNetwork();

        $this->assertInstanceOf(NetworkInterface::class, $network);
    }

    #[TestDox('returns correct properties from API response')]
    public function testProperties(): void
    {
        $network = $this->createTwitterNetwork();

        $this->assertEquals(108, $network->getFeedsApiId());
        $this->assertEquals('twitter', $network->getIdentifier());
        $this->assertEquals('Twitter', $network->getName());
        $this->assertEquals('fab fa-twitter', $network->getIcon());
        $this->assertEquals('rgb(29, 161, 242)', $network->getBackgroundColor());
        $this->assertEquals('white', $network->getTextColor());
    }

    #[TestDox('generates correct IRI')]
    public function testIri(): void
    {
        $network = $this->createTwitterNetwork();

        $this->assertEquals('/api/networks/108', $network->getIri());
    }

    #[TestDox('accepts matching Twitter URLs')]
    public function testAcceptsTwitterUrl(): void
    {
        $network = $this->createTwitterNetwork();

        $this->assertTrue($network->accepts('https://twitter.com/criticalmassHH'));
        $this->assertTrue($network->accepts('https://www.twitter.com/cm_hh'));
        $this->assertTrue($network->accepts('http://twitter.com/cm_hh'));
        $this->assertTrue($network->accepts('https://x.com/criticalmassHH'));
    }

    #[TestDox('rejects non-matching URLs')]
    public function testRejectsNonMatchingUrl(): void
    {
        $network = $this->createTwitterNetwork();

        $this->assertFalse($network->accepts('https://instagram.com/criticalmass'));
        $this->assertFalse($network->accepts('https://facebook.com/criticalmass'));
        $this->assertFalse($network->accepts('@criticalmass'));
    }

    #[TestDox('homepage network has lowest detector priority')]
    public function testHomepageDetectorPriority(): void
    {
        $homepage = Network::fromApiResponse([
            'id' => 101,
            'identifier' => 'homepage',
            'name' => 'Homepage',
            'icon' => 'fas fa-house',
            'backgroundColor' => 'white',
            'textColor' => 'black',
            'profileUrlPattern' => '#^https?://.+$#i',
        ]);

        $this->assertEquals(-100, $homepage->getDetectorPriority());
    }

    #[TestDox('youtube_video has lower priority than regular networks')]
    public function testYoutubeVideoDetectorPriority(): void
    {
        $youtube = Network::fromApiResponse([
            'id' => 125,
            'identifier' => 'youtube_video',
            'name' => 'YouTube-Video',
            'icon' => 'fab fa-youtube',
            'backgroundColor' => 'rgb(255, 0, 0)',
            'textColor' => 'white',
            'profileUrlPattern' => '#^((?:https?:)?//)?((?:www|m)\.)?(youtube\.com|youtu\.be)#i',
        ]);

        $this->assertEquals(-10, $youtube->getDetectorPriority());
    }

    #[TestDox('regular networks have priority 0')]
    public function testRegularDetectorPriority(): void
    {
        $network = $this->createTwitterNetwork();

        $this->assertEquals(0, $network->getDetectorPriority());
    }

    #[TestDox('handles missing profileUrlPattern gracefully')]
    public function testMissingProfileUrlPattern(): void
    {
        $network = Network::fromApiResponse([
            'id' => 999,
            'identifier' => 'test',
            'name' => 'Test',
            'icon' => 'fas fa-test',
            'backgroundColor' => 'white',
            'textColor' => 'black',
        ]);

        $this->assertFalse($network->accepts('https://example.com'));
    }
}
