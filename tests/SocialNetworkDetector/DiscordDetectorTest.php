<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class DiscordDetectorTest extends AbstractNetworkDetectorTest
{
    public function testDiscordChat(): void
    {
        $network = $this->detect('https://discordapp.com/invite/WEgc3436ew');

        $this->assertEquals('discord_chat', $network->getIdentifier());

        $network = $this->detect('https://discord.gg/WEgc3436ew');

        $this->assertEquals('discord_chat', $network->getIdentifier());
    }
}
