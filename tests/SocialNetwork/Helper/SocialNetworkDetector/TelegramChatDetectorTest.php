<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class TelegramChatDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testTelegramChat(): void
    {
        $network = $this->detect('https://t.me/WEgc3436ew');

        $this->assertEquals('telegram_chat', $network->getIdentifier());

        $network = $this->detect('http://t.me/WEgc3436ew');

        $this->assertEquals('telegram_chat', $network->getIdentifier());
    }
}
