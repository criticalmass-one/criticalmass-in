<?php declare(strict_types=1);

namespace Tests\ViewStorage\BlackList;

use App\Criticalmass\ViewStorage\BlackList\BlackList;
use PHPUnit\Framework\TestCase;

class BlackListTest extends TestCase
{
    protected function createBlackList(): BlackList
    {
        return new BlackList();
    }

    public function testBlackListUptimeRobot(): void
    {
        $userAgent = 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)';

        $this->assertTrue($this->createBlackList()->isBlackListed($userAgent));
    }

    public function testBlackListSemrushBot(): void
    {
        $userAgent = 'Mozilla/5.0 (compatible; SemrushBot/6~bl; +http://www.semrush.com/bot.html)';

        $this->assertTrue($this->createBlackList()->isBlackListed($userAgent));
    }

    public function testBlackListGoogleBot1(): void
    {
        $userAgent = 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

        $this->assertTrue($this->createBlackList()->isBlackListed($userAgent));
    }

    public function testBlackListMsnBot(): void
    {
        $userAgent = 'msnbot/2.0b (+http://search.msn.com/msnbot.htm)';

        $this->assertTrue($this->createBlackList()->isBlackListed($userAgent));
    }

    public function testBlackListGoogleImageBot(): void
    {
        $userAgent = 'Googlebot-Image/1.0';

        $this->assertTrue($this->createBlackList()->isBlackListed($userAgent));
    }

    public function testNotBlackListedNormalBrowser(): void
    {
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

        $this->assertFalse($this->createBlackList()->isBlackListed($userAgent));
    }
}
