<?php declare(strict_types=1);

namespace Tests\Mcp;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\CitySlug;
use App\Entity\Location;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Subride;
use App\Entity\Track;
use App\Entity\User;
use App\OAuth2\OAuthScope;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Basis für MCP-Integrationstests: meldet einen User an, registriert einen
 * OAuth-Client mit allen Scopes und liefert Helfer für den Auth-Code-Flow,
 * JSON-RPC-Aufrufe und das Anlegen von Test-Entities.
 */
abstract class AbstractMcpTestCase extends WebTestCase
{
    protected const REDIRECT_URI = 'https://connector.example.org/callback';

    protected KernelBrowser $client;
    protected User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->client->catchExceptions(false);

        // Ohne dama/doctrine-test-bundle: jede Testmethode in eine Transaktion
        // hüllen und am Ende zurückrollen, damit Tests isoliert bleiben.
        $this->em()->getConnection()->beginTransaction();

        // Eindeutige E-Mail je Test: der OAuth-Provider lädt den Token-User per
        // E-Mail; ohne Eindeutigkeit kollidiert er mit (commit-ten) Usern anderer
        // Testklassen, die keine Transaktions-Isolation nutzen.
        $this->user = new User();
        $this->user->setUsername('mcp-tester');
        $this->user->setEmail(uniqid('mcp-', true) . '@example.org');
        $this->user->setRoles(['ROLE_USER']);
        $this->user->setEnabled(true);
        $this->em()->persist($this->user);
        $this->em()->flush();

        $this->client->loginUser($this->user, 'user');

        /** @var ClientManagerInterface $clientManager */
        $clientManager = $this->client->getContainer()->get(ClientManagerInterface::class);
        $oauthClient = new Client('MCP Test', 'mcp-test', null);
        $oauthClient->setActive(true);
        $oauthClient->setGrants(new Grant('authorization_code'), new Grant('refresh_token'));
        $oauthClient->setRedirectUris(new RedirectUri(self::REDIRECT_URI));
        $oauthClient->setScopes(...array_map(static fn (string $s): Scope => new Scope($s), OAuthScope::values()));
        $clientManager->save($oauthClient);
    }

    protected function tearDown(): void
    {
        $connection = $this->em()->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    protected function em(): EntityManagerInterface
    {
        return $this->client->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Führt eine JSON-RPC-Anfrage gegen /mcp aus und gibt die dekodierte Antwort zurück.
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    protected function rpc(string $token, string $method, array $params = [], int $id = 1): array
    {
        $body = ['jsonrpc' => '2.0', 'id' => $id, 'method' => $method];
        if ([] !== $params) {
            $body['params'] = $params;
        }

        $this->client->request('POST', '/mcp', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($body, JSON_THROW_ON_ERROR));

        return json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Ruft ein Tool auf und gibt das (dekodierte) Text-Ergebnis bzw. das
     * result-Objekt zurück.
     *
     * @param array<string, mixed> $arguments
     *
     * @return array{isError: bool, text: string, json: mixed}
     */
    protected function callTool(string $token, string $name, array $arguments = []): array
    {
        $result = $this->rpc($token, 'tools/call', ['name' => $name, 'arguments' => $arguments])['result'];
        $text = $result['content'][0]['text'];

        return [
            'isError' => $result['isError'],
            'text' => $text,
            'json' => json_decode($text, true),
        ];
    }

    protected function obtainAccessToken(string $scope): string
    {
        $verifier = 'mcp-poc-verifier-0123456789-0123456789-0123456789';
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        $authorizeUrl = '/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => 'mcp-test',
            'redirect_uri' => self::REDIRECT_URI,
            'scope' => $scope,
            'state' => 'state-123',
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ]);

        $crawler = $this->client->request('GET', $authorizeUrl);
        $consentAuthorizeUrl = $crawler->filter('input[name="authorize_url"]')->attr('value');

        $this->client->request('POST', '/oauth2/consent', [
            'client_id' => 'mcp-test',
            'authorize_url' => $consentAuthorizeUrl,
            'consent' => 'approve',
        ]);
        $this->client->followRedirect();

        $location = (string) $this->client->getResponse()->headers->get('Location');
        parse_str((string) parse_url($location, PHP_URL_QUERY), $callbackParams);

        $this->client->request('POST', '/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'mcp-test',
            'redirect_uri' => self::REDIRECT_URI,
            'code' => $callbackParams['code'],
            'code_verifier' => $verifier,
        ]);

        $token = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return (string) $token['access_token'];
    }

    protected function createCity(string $name = 'Hamburg', string $slug = 'hamburg'): City
    {
        $city = new City();
        $city->setCity($name);
        $city->setTitle('Critical Mass ' . $name);
        $city->setCreatedAt(new \DateTime());
        $this->em()->persist($city);

        $citySlug = new CitySlug();
        $citySlug->setSlug($slug);
        $citySlug->setCity($city);
        $this->em()->persist($citySlug);

        $city->setMainSlug($citySlug);

        $this->em()->flush();

        return $city;
    }

    protected function createRide(City $city, string $date = '2026-09-01 19:00:00', string $title = 'Critical Mass'): Ride
    {
        $ride = new Ride();
        $ride->setCity($city);
        $ride->setDateTime(new \DateTime($date));
        $ride->setTitle($title);
        $this->em()->persist($ride);
        $this->em()->flush();

        return $ride;
    }

    protected function createTrack(Ride $ride): Track
    {
        $track = new Track();
        $track->setRide($ride);
        $track->setUser($this->user);
        $track->setUsername('mcp-tester');
        $track->setEnabled(true);
        $track->setDeleted(false);
        $this->em()->persist($track);
        $this->em()->flush();

        return $track;
    }

    protected function createPhoto(Ride $ride, City $city): Photo
    {
        $photo = new Photo();
        $photo->setRide($ride);
        $photo->setCity($city);
        $photo->setUser($this->user);
        $photo->setImageName('test.jpg');
        $photo->setCreationDateTime(new \DateTime());
        $photo->setEnabled(true);
        $photo->setDeleted(false);
        $this->em()->persist($photo);
        $this->em()->flush();

        return $photo;
    }

    protected function createLocation(City $city, string $slug = 'treffpunkt'): Location
    {
        $location = new Location();
        $location->setCity($city);
        $location->setTitle('Treffpunkt');
        $location->setSlug($slug);
        $this->em()->persist($location);
        $this->em()->flush();

        return $location;
    }

    protected function createSubride(Ride $ride): Subride
    {
        $subride = new Subride();
        $subride->setRide($ride);
        $subride->setTitle('Anfahrt Nord');
        $subride->setDateTime(new \DateTime('2026-09-01 18:00:00'));
        $this->em()->persist($subride);
        $this->em()->flush();

        return $subride;
    }

    protected function createCityCycle(City $city): CityCycle
    {
        $cycle = new CityCycle();
        $cycle->setCity($city);
        $cycle->setDayOfWeek(5);
        $cycle->setWeekOfMonth(0);
        $this->em()->persist($cycle);
        $this->em()->flush();

        return $cycle;
    }
}
