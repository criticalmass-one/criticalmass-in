<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Ride;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class StravaControllerTest extends AbstractControllerTestCase
{
    private function rideUrl(KernelBrowser $client, string $action): string
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $this->loginAs($client, $user->getEmail());

        $ride = $entityManager->getRepository(Ride::class)->findOneBy(['slug' => null]);
        $this->assertNotNull($ride);

        return sprintf('/%s/%s/%s', $ride->getCity()->getMainSlugString(), $ride->getDateTime()->format('Y-m-d'), $action);
    }

    public function testDeniedAuthorizationLeadsBackToTheConnectPage(): void
    {
        $client = static::createClient();
        $url = $this->rideUrl($client, 'stravatoken');

        $client->request('GET', $url . '?state=&error=access_denied');

        $this->assertResponseRedirects(str_replace('stravatoken', 'stravaauth', $url));
    }

    public function testTheListWithoutTokenLeadsBackToTheConnectPage(): void
    {
        $client = static::createClient();
        $url = $this->rideUrl($client, 'stravalist');

        $client->request('GET', $url);

        $this->assertResponseRedirects(str_replace('stravalist', 'stravaauth', $url));
    }

    public function testTheConnectPageShowsTheStravaButton(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $this->rideUrl($client, 'stravaauth'));

        $this->assertResponseIsSuccessful();
        $this->assertStringStartsWith('https://www.strava.com/oauth/authorize', (string) $crawler->filter('a.strava-connect')->attr('href'));
    }

    public function testTheMassImportIsGone(): void
    {
        static::createClient();
        $routes = static::getContainer()->get('router')->getRouteCollection();

        $this->assertNull($routes->get('caldera_criticalmass_trackmassimport_auth'));
    }
}
