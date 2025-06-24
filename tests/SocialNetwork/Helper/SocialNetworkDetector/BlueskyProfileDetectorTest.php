<?php declare(strict_types=1);

namespace SocialNetwork\Helper\SocialNetworkDetector;

use Tests\SocialNetworkDetector\AbstractNetworkDetectorTest;

class BlueskyProfileDetectorTest extends AbstractNetworkDetectorTest
{
    public function testBlueskyProfile(): void
    {
        $network = $this->detect('https://bsky.app/profile/did:plc:wwrjssxbvzpqoljk5yq67jey');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());

        $network = $this->detect('https://bsky.app/profile/did:plc:zvl5hg74ophhey7rgmnxilfy');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());

        $network = $this->detect('https://bsky.app/profile/did:plc:6qlgxb2d5qg62j5dlnrnlunm');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());

        $network = $this->detect('https://bsky.app/profile/did:plc:p5dyfgiy7iksulo4pkgm7yfu');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());

        $network = $this->detect('https://bsky.app/profile/did:plc:lrxmkcfgiyj3lb7sphsziybh');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());

        $network = $this->detect('https://bsky.app/profile/did:plc:xspvw6iilubalhek6xywqxro');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());

        $network = $this->detect('https://bsky.app/profile/did:plc:vwtnifhk6p4ephuezaxwbtm6');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());

        $network = $this->detect('https://bsky.app/profile/did:plc:i6btmhx26vrmvgkjsnhf4lpf');

        $this->assertEquals('bluesky_profile', $network->getIdentifier());
    }
}
