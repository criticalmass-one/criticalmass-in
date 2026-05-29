<?php declare(strict_types=1);

namespace Tests\SocialNetwork\Helper\SocialNetworkDetector;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedsNetworkManager;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetector;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetectorInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractNetworkDetectorTestCase extends TestCase
{
    protected function getNetworkDetector(): NetworkDetectorInterface
    {
        $networkManager = $this->createNetworkManager();

        return new NetworkDetector($networkManager);
    }

    private function createNetworkManager(): NetworkManagerInterface
    {
        $networks = $this->getNetworkDefinitions();

        $manager = new class($networks) implements NetworkManagerInterface {
            /** @var array<string, NetworkInterface> */
            private array $networkList;

            public function __construct(array $networks)
            {
                $this->networkList = [];
                foreach ($networks as $network) {
                    $this->networkList[$network->getIdentifier()] = $network;
                }
            }

            public function getNetworkList(): array
            {
                return $this->networkList;
            }

            public function hasNetwork(string $identifier): bool
            {
                return isset($this->networkList[$identifier]);
            }

            public function getNetwork(string $identifier): NetworkInterface
            {
                return $this->networkList[$identifier];
            }
        };

        return $manager;
    }

    /** @return Network[] */
    private function getNetworkDefinitions(): array
    {
        return [
            Network::fromApiResponse(['id' => 101, 'identifier' => 'homepage', 'name' => 'Homepage', 'icon' => 'fas fa-house', 'backgroundColor' => 'white', 'textColor' => 'black', 'profileUrlPattern' => '#^https?://.+$#i']),
            Network::fromApiResponse(['id' => 102, 'identifier' => 'facebook_profile', 'name' => 'Facebook-Profil', 'icon' => 'fab fa-facebook-f', 'backgroundColor' => 'rgb(60, 88, 152)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?facebook\.com/profile\.php\?id=\d+.*$#i']),
            Network::fromApiResponse(['id' => 103, 'identifier' => 'facebook_group', 'name' => 'Facebook-Gruppe', 'icon' => 'fab fa-facebook-f', 'backgroundColor' => 'rgb(60, 88, 152)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?facebook\.com/groups/[^/?#]+/?$#i']),
            Network::fromApiResponse(['id' => 104, 'identifier' => 'facebook_event', 'name' => 'Facebook-Event', 'icon' => 'fab fa-facebook-f', 'backgroundColor' => 'rgb(60, 88, 152)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?facebook\.com/events/[^/?#]+/?$#i']),
            Network::fromApiResponse(['id' => 105, 'identifier' => 'facebook_page', 'name' => 'Facebook-Seite', 'icon' => 'fab fa-facebook-f', 'backgroundColor' => 'rgb(60, 88, 152)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?facebook\.com/(?!groups/|events/|profile\.php)([A-Za-z0-9.\-]+)/?$#i']),
            Network::fromApiResponse(['id' => 106, 'identifier' => 'instagram_profile', 'name' => 'Instagram-Profil', 'icon' => 'fab fa-instagram', 'backgroundColor' => 'rgb(203, 44, 128)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?instagram\.[A-Za-z]{2,3}/[A-Za-z0-9\-_]{5,}/?$#i']),
            Network::fromApiResponse(['id' => 107, 'identifier' => 'instagram_photo', 'name' => 'Instagram-Foto', 'icon' => 'fab fa-instagram', 'backgroundColor' => 'rgb(203, 44, 128)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?instagram\.com/p/[^/?#]+/?$#i']),
            Network::fromApiResponse(['id' => 108, 'identifier' => 'twitter', 'name' => 'Twitter', 'icon' => 'fab fa-twitter', 'backgroundColor' => 'rgb(29, 161, 242)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?(twitter|x)\.com/[A-Za-z0-9_]+/?$#i']),
            Network::fromApiResponse(['id' => 109, 'identifier' => 'mastodon', 'name' => 'Mastodon', 'icon' => 'fab fa-mastodon', 'backgroundColor' => 'rgb(96, 94, 239)', 'textColor' => 'white', 'profileUrlPattern' => '#^(@?[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}|https?://[A-Za-z0-9.\-]+/@[A-Za-z0-9_]+/?$)#i']),
            Network::fromApiResponse(['id' => 110, 'identifier' => 'bluesky_profile', 'name' => 'Bluesky-Profil', 'icon' => 'fab fa-bluesky', 'backgroundColor' => '#0276ff', 'textColor' => 'white', 'profileUrlPattern' => '#^(https?://bsky\.app/profile/(did:plc:[a-z0-9]+|[a-z0-9.\-]+\.[a-z]{2,})/?|[a-z0-9.\-]+\.[a-z]{2,})$#i']),
            Network::fromApiResponse(['id' => 111, 'identifier' => 'threads_profile', 'name' => 'Threads-Profil', 'icon' => 'fab fa-threads', 'backgroundColor' => '#000000', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?threads\.(net|com)/@[\w.]+/?$#i']),
            Network::fromApiResponse(['id' => 112, 'identifier' => 'threads_post', 'name' => 'Threads-Beitrag', 'icon' => 'fab fa-threads', 'backgroundColor' => '#000000', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?threads\.(net|com)/@[\w.]+/post/[0-9]+/?$#i']),
            Network::fromApiResponse(['id' => 113, 'identifier' => 'discord_chat', 'name' => 'Discord-Chat', 'icon' => 'fab fa-discord', 'backgroundColor' => 'rgb(114, 137, 218)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://discord(app\.com/|\.gg/).+$#i']),
            Network::fromApiResponse(['id' => 114, 'identifier' => 'telegram_chat', 'name' => 'Telegram-Chat', 'icon' => 'fab fa-telegram-plane', 'backgroundColor' => 'rgb(40, 159, 217)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://t\.me/.+$#i']),
            Network::fromApiResponse(['id' => 115, 'identifier' => 'whatsapp_chat', 'name' => 'WhatsApp-Chat', 'icon' => 'fab fa-whatsapp', 'backgroundColor' => 'rgb(65, 193, 83)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://chat\.whatsapp\.com/.+$#i']),
            Network::fromApiResponse(['id' => 116, 'identifier' => 'flickr', 'name' => 'flickr', 'icon' => 'fab fa-flickr', 'backgroundColor' => 'rgb(12, 101, 211)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?flickr\.com/photos/.+$#i']),
            Network::fromApiResponse(['id' => 117, 'identifier' => 'tumblr', 'name' => 'Tumblr', 'icon' => 'fab fa-tumblr', 'backgroundColor' => 'rgb(0, 0, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?[A-Za-z0-9]*\.tumblr\.com/?$#i']),
            Network::fromApiResponse(['id' => 118, 'identifier' => 'google', 'name' => 'Google+', 'icon' => 'fab fa-google-plus-g', 'backgroundColor' => 'rgb(234, 66, 53)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?plus\.google\.com/\+[A-Za-z0-9\-]+/?$#i']),
            Network::fromApiResponse(['id' => 119, 'identifier' => 'strava_activity', 'name' => 'Strava-Aktivität', 'icon' => 'fab fa-strava', 'backgroundColor' => 'rgb(252, 82, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?strava\.com/activities/\d+/?$#i']),
            Network::fromApiResponse(['id' => 120, 'identifier' => 'strava_club', 'name' => 'Strava-Club', 'icon' => 'fab fa-strava', 'backgroundColor' => 'rgb(252, 82, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?strava\.com/clubs/\d+/?$#i']),
            Network::fromApiResponse(['id' => 121, 'identifier' => 'strava_route', 'name' => 'Strava-Route', 'icon' => 'fab fa-strava', 'backgroundColor' => 'rgb(252, 82, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?strava\.com/routes/\d+/?$#i']),
            Network::fromApiResponse(['id' => 122, 'identifier' => 'youtube_channel', 'name' => 'YouTube', 'icon' => 'fab fa-youtube', 'backgroundColor' => 'rgb(255, 0, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?youtube\.com/channel/.+$#i']),
            Network::fromApiResponse(['id' => 123, 'identifier' => 'youtube_user', 'name' => 'YouTube-Konto', 'icon' => 'fab fa-youtube', 'backgroundColor' => 'rgb(255, 0, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?youtube\.com/user/.+$#i']),
            Network::fromApiResponse(['id' => 124, 'identifier' => 'youtube_playlist', 'name' => 'YouTube-Playlist', 'icon' => 'fab fa-youtube', 'backgroundColor' => 'rgb(255, 0, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^https?://(www\.)?youtube\.com/playlist\?.+$#i']),
            Network::fromApiResponse(['id' => 125, 'identifier' => 'youtube_video', 'name' => 'YouTube-Video', 'icon' => 'fab fa-youtube', 'backgroundColor' => 'rgb(255, 0, 0)', 'textColor' => 'white', 'profileUrlPattern' => '#^((?:https?:)?//)?((?:www|m)\.)?((?:youtube\.com|youtu\.be))(\/(?:watch+\?v=|embed\/|v\/)?)([\\w\\-]+)(\\S+)?$#i']),
        ];
    }

    protected function detect(string $url): ?NetworkInterface
    {
        $network = $this->getNetworkDetector()->detect($url);

        return $network;
    }
}
