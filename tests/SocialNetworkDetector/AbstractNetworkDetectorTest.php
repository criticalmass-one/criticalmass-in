<?php declare(strict_types=1);

namespace Tests\SocialNetworkDetector;

use App\Criticalmass\SocialNetwork\Network;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetector;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetectorInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManager;
use PHPUnit\Framework\TestCase;

abstract class AbstractNetworkDetectorTest extends TestCase
{
    protected function getNetworkDetector(): NetworkDetectorInterface
    {
        $networkManager = new NetworkManager();
        //$networkManager->addNetwork(new Network\FacebookEvent());
        //$networkManager->addNetwork(new Network\FacebookGroup());
        //$networkManager->addNetwork(new Network\FacebookPage());
        $networkManager->addNetwork(new Network\FacebookProfile());
        $networkManager->addNetwork(new Network\DiscordChat());
        $networkManager->addNetwork(new Network\Flickr());
        $networkManager->addNetwork(new Network\Google());
        $networkManager->addNetwork(new Network\Homepage());
        $networkManager->addNetwork(new Network\Tumblr());
        $networkManager->addNetwork(new Network\TelegramChat());
        $networkManager->addNetwork(new Network\Twitter());
        $networkManager->addNetwork(new Network\InstagramPhoto());
        $networkManager->addNetwork(new Network\InstagramProfile());
        $networkManager->addNetwork(new Network\YoutubeChannel());
        $networkManager->addNetwork(new Network\YoutubePlaylist());
        $networkManager->addNetwork(new Network\YoutubeUser());
        $networkManager->addNetwork(new Network\YoutubeVideo());
        $networkManager->addNetwork(new Network\WhatsappChat());

        return new NetworkDetector($networkManager);
    }

    protected function detect(string $url): ?Network\NetworkInterface
    {
        $network = $this->getNetworkDetector()->detect($url);

        return $network;
    }
}
