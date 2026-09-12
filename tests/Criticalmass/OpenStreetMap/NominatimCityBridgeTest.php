<?php declare(strict_types=1);

namespace Tests\Criticalmass\OpenStreetMap;

use App\Criticalmass\OpenStreetMap\NominatimCityBridge\NominatimCityBridge;
use App\Entity\City;
use App\Entity\Region;
use App\Factory\City\CityFactoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * Was criticalmass.in nach draussen schickt, wenn jemand einen unbekannten
 * Pfad aufruft.
 *
 * Die Route /{citySlug} faengt **jeden** unbekannten Pfad ab und fragt
 * Nominatim, ob es dazu eine Stadt gibt. Am 12.09.2026 klapperte ein Crawler
 * Karriereseiten ab -- /work-here, /work-for-us, /positions -- und
 * criticalmass.in reichte jede dieser Erfindungen an OpenStreetMap weiter.
 * Von dort kam ein 429, und weil die Ausnahme niemand auffing, wurde aus der
 * Abfuhr eines fremden Dienstes ein Serverfehler auf unserer Seite.
 *
 * Die Tests bewachen beide Seiten davon: dass eine Abfuhr die Seite nicht
 * mehr umwirft, und dass wir gar nicht erst so viel fragen.
 */
class NominatimCityBridgeTest extends TestCase
{
    private function bruecke(MockHttpClient $client, int $kontingent = 60): NominatimCityBridge
    {
        $region = new Region();
        $region->setName('Hamburg');

        // findOneByName() ist eine magische Methode von Doctrines
        // EntityRepository -- PHPUnit kann sie nicht einrichten, weil es sie
        // gar nicht gibt. Also von Hand, und dabei gleich die ganze
        // Schnittstelle, weil getRepository() sie als Rueckgabetyp fordert.
        $repository = new class($region) implements ObjectRepository {
            public function __construct(private readonly Region $region)
            {
            }

            public function findOneByName(string $name): ?Region
            {
                return 'Hamburg' === $name ? $this->region : null;
            }

            public function find($id): ?object
            {
                return null;
            }

            public function findAll(): array
            {
                return [];
            }

            public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
            {
                return [];
            }

            public function findOneBy(array $criteria): ?object
            {
                return null;
            }

            public function getClassName(): string
            {
                return Region::class;
            }
        };

        $doctrine = $this->createMock(ManagerRegistry::class);
        $doctrine->method('getRepository')->willReturn($repository);

        $stadt = new City();
        $stadt->setCity('Hamburg');

        $factory = $this->createMock(CityFactoryInterface::class);
        foreach (['withLatitude', 'withLongitude', 'withName', 'withTitle', 'withRegion'] as $methode) {
            $factory->method($methode)->willReturnSelf();
        }
        $factory->method('build')->willReturn($stadt);

        return new NominatimCityBridge(
            $doctrine,
            $factory,
            $client,
            new ArrayAdapter(),
            new RateLimiterFactory(
                ['id' => 'nominatim', 'policy' => 'token_bucket', 'limit' => $kontingent, 'rate' => ['interval' => '1 second', 'amount' => 1]],
                new InMemoryStorage()
            ),
            new NullLogger(),
        );
    }

    private function treffer(): MockResponse
    {
        return new MockResponse(json_encode([[
            'lat' => '53.5503',
            'lon' => '9.9920',
            'address' => ['city' => 'Hamburg', 'state' => 'Hamburg'],
        ]]), ['http_code' => 200]);
    }

    private function leer(): MockResponse
    {
        return new MockResponse('[]', ['http_code' => 200]);
    }

    public function testAKnownCityIsStillLookedUp(): void
    {
        $bruecke = $this->bruecke(new MockHttpClient([$this->treffer()]));

        self::assertInstanceOf(City::class, $bruecke->lookupCity('hamburg'));
    }

    /**
     * Der Fall aus dem Protokoll: Nominatim weist uns ab.
     */
    public function testARefusalFromNominatimDoesNotBreakThePage(): void
    {
        $bruecke = $this->bruecke(new MockHttpClient([new MockResponse('', ['http_code' => 429])]));

        self::assertNull($bruecke->lookupCity('work-here'));
    }

    public function testAServerErrorAtNominatimDoesNotBreakThePage(): void
    {
        $bruecke = $this->bruecke(new MockHttpClient([new MockResponse('', ['http_code' => 503])]));

        self::assertNull($bruecke->lookupCity('positions'));
    }

    public function testATransportFailureDoesNotBreakThePage(): void
    {
        $bruecke = $this->bruecke(new MockHttpClient([
            new MockResponse(['', ''], ['error' => 'Connection timed out']),
        ]));

        self::assertNull($bruecke->lookupCity('work-for-us'));
    }

    /**
     * Zweimal derselbe Pfad, einmal gefragt.
     */
    public function testTheSameCityIsOnlyAskedOnce(): void
    {
        $client = new MockHttpClient([$this->treffer(), $this->treffer()]);
        $bruecke = $this->bruecke($client);

        $bruecke->lookupCity('hamburg');
        $bruecke->lookupCity('hamburg');

        self::assertSame(1, $client->getRequestsCount());
    }

    /**
     * Und das gilt gerade fuer die leeren Antworten -- die kommen von den
     * unsinnigen Pfaden, und genau die wiederholen sich.
     */
    public function testAnEmptyAnswerIsRememberedToo(): void
    {
        $client = new MockHttpClient([$this->leer(), $this->leer()]);
        $bruecke = $this->bruecke($client);

        self::assertNull($bruecke->lookupCity('work-here'));
        self::assertNull($bruecke->lookupCity('work-here'));

        self::assertSame(1, $client->getRequestsCount());
    }

    /**
     * Der Kern: Ist unser eigenes Kontingent erschoepft, geht gar nichts mehr
     * hinaus. Nicht, um uns zu schuetzen -- um OpenStreetMap vor uns zu
     * schuetzen.
     */
    public function testAnExhaustedBudgetStopsUsFromAskingAtAll(): void
    {
        $client = new MockHttpClient([$this->treffer(), $this->treffer(), $this->treffer()]);
        $bruecke = $this->bruecke($client, kontingent: 2);

        self::assertInstanceOf(City::class, $bruecke->lookupCity('hamburg'));
        self::assertInstanceOf(City::class, $bruecke->lookupCity('bremen'));
        self::assertNull($bruecke->lookupCity('luebeck'), 'Die dritte Anfrage unterbleibt.');

        self::assertSame(2, $client->getRequestsCount());
    }

    /**
     * Nominatim verlangt eine Kennung, unter der man erreichbar ist -- eine
     * blosse Versionsnummer erfuellt das nicht.
     *
     * Hier faengt eine Rueckruffunktion die Anfrage ab, statt eine fertige
     * Antwort zu liefern: Nur so kommt man an die abgeschickten Kopfzeilen.
     */
    public function testWeIdentifyOurselvesToNominatim(): void
    {
        $gesehen = [];

        $client = new MockHttpClient(static function (string $methode, string $url, array $optionen) use (&$gesehen): MockResponse {
            $gesehen = ['methode' => $methode, 'url' => $url, 'kopfzeilen' => $optionen['headers'] ?? []];

            return new MockResponse('[]', ['http_code' => 200]);
        });

        $this->bruecke($client)->lookupCity('hamburg');

        $kennung = implode(' ', array_filter(
            $gesehen['kopfzeilen'],
            static fn (string $zeile): bool => str_starts_with(strtolower($zeile), 'user-agent:')
        ));

        self::assertStringContainsString('criticalmass.in', $kennung);
        self::assertStringContainsString('https://', $kennung, 'Ohne Kontaktadresse ist die Kennung wertlos.');
        self::assertStringContainsString('city=hamburg', $gesehen['url']);
    }
}
