<?php declare(strict_types=1);

namespace Tests\SocialNetwork;

use App\Criticalmass\SocialNetwork\Network;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetector;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetectorInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManager;
use App\Entity\SocialNetworkProfile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SocialNetworkTest extends KernelTestCase
{
    protected function getNetworkDetector(): NetworkDetectorInterface
    {
        $networkManager = new NetworkManager();
        //$networkManager->addNetwork(new Network\FacebookEvent());
        //$networkManager->addNetwork(new Network\FacebookGroup());
        //$networkManager->addNetwork(new Network\FacebookPage());
        //$networkManager->addNetwork(new Network\FacebookProfile());
        $networkManager->addNetwork(new Network\Flickr());
        $networkManager->addNetwork(new Network\Google());
        $networkManager->addNetwork(new Network\Homepage());
        $networkManager->addNetwork(new Network\Tumblr());
        $networkManager->addNetwork(new Network\Twitter());
        $networkManager->addNetwork(new Network\Youtube());

        return new NetworkDetector($networkManager);
    }

    protected function createProfile(string $identifier): SocialNetworkProfile
    {
        $profile = new SocialNetworkProfile();

        $profile->setIdentifier($identifier);

        return $profile;
    }

    protected function createAndDetect(string $identifier): ?Network\NetworkInterface
    {
        $profile = $this->createProfile($identifier);

        $network = $this->getNetworkDetector()->detect($profile);

        return $network;
    }

    public function testHomepage(): void
    {
        $network = $this->createAndDetect('https://criticalmass-hamburg.de/');

        $this->assertEquals('homepage', $network->getIdentifier());

        $network = $this->createAndDetect('http://criticalmass-hamburg.de/');

        $this->assertEquals('homepage', $network->getIdentifier());

        $network = $this->createAndDetect('https://criticalmass-hamburg.de');

        $this->assertEquals('homepage', $network->getIdentifier());

        $network = $this->createAndDetect('criticalmass-hamburg.de/');

        $this->assertNull($network);
    }

    public function testGoogle(): void
    {
        $network = $this->createAndDetect('https://plus.google.com/+Critical-Mass-Hamburg');

        $this->assertEquals('google', $network->getIdentifier());
    }

    public function testFlickr(): void
    {
        $network = $this->createAndDetect('https://www.flickr.com/photos/130278554@N08/');

        $this->assertEquals('flickr', $network->getIdentifier());
    }

    public function testTumblr(): void
    {
        $network = $this->createAndDetect('https://criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());

        $network = $this->createAndDetect('http://criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());

        $network = $this->createAndDetect('https://www.criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());

        $network = $this->createAndDetect('http://www.criticalmasshamburg.tumblr.com/');

        $this->assertEquals('tumblr', $network->getIdentifier());
    }

    public function testTwitter(): void
    {
        $network = $this->createAndDetect('https://twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->createAndDetect('https://www.twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->createAndDetect('http://twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->createAndDetect('http://www.twitter.com/cm_hh');

        $this->assertEquals('twitter', $network->getIdentifier());

        $network = $this->createAndDetect('@cm_hh');

        $this->assertNull($network);
    }

    public function testYoutube(): void
    {
        $network = $this->createAndDetect('https://www.youtube.com/channel/UCq3Ci-h945sbEYXpVlw7rJg');

        $this->assertEquals('youtube', $network->getIdentifier());
    }
}
