<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

class WhatsappChatDetectorTest extends AbstractNetworkDetectorTestCase
{
    public function testWhatsAppChat(): void
    {
        $network = $this->detect('https://chat.whatsapp.com/WEgc3436ew');

        $this->assertEquals('whatsapp_chat', $network->getIdentifier());

        $network = $this->detect('http://chat.whatsapp.com/WEgc3436ew');

        $this->assertEquals('whatsapp_chat', $network->getIdentifier());
    }
}
