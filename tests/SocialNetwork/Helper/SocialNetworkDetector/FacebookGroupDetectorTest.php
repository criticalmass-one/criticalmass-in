<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class FacebookGroupDetectorTest extends AbstractNetworkDetectorTest
{
    public function testFacebookGroup(): void
    {
        $network = $this->detect('https://www.facebook.com/groups/alltagsradeln/');

        $this->assertEquals('facebook_group', $network->getIdentifier());

        $network = $this->detect('https://www.facebook.com/groups/CMHH.Diskussion/?ref=nf_target&fref=nf');

        $this->assertEquals('facebook_group', $network->getIdentifier());
        
        $network = $this->detect('https://www.facebook.com/groups/983621838650464/');

        $this->assertEquals('facebook_group', $network->getIdentifier());
    }
}
