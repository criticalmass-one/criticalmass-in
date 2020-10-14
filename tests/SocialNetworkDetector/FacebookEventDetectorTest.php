<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

class FacebookEventDetectorTest extends AbstractNetworkDetectorTest
{
    public function testFacebookGroup(): void
    {
        $network = $this->detect('https://www.facebook.com/events/1153532054978391/');

        $this->assertEquals('facebook_event', $network->getIdentifier());

        $network = $this->detect('https://www.facebook.com/events/945982289213686/?acontext=%7B%22ref%22%3A%223%22%2C%22ref_newsfeed_story_type%22%3A%22regular%22%2C%22feed_story_type%22%3A%22361%22%2C%22action_history%22%3A%22%5B%7B%5C%22surface%5C%22%3A%5C%22newsfeed%5C%22%2C%5C%22mechanism%5C%22%3A%5C%22feed_story%5C%22%2C%5C%22extra_data%5C%22%3A%5B%5D%7D%5D%22%7D');

        $this->assertEquals('facebook_event', $network->getIdentifier());
    }
}
