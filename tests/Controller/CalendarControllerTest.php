<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\City;
use App\Entity\Ride;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Der Kalender.
 *
 * Die Touren verteilen sich extrem ungleich: Im September 2026 stehen an 28
 * Tagen zusammen 310 Touren — im Mittel zwei je Tag, aber **145 allein am
 * letzten Freitag**. Der frueheren Ansicht gab jeder Tag dieselben 175 Pixel
 * und einen Rollbalken; sie verschenkte Flaeche an leere Tage und versteckte
 * ausgerechnet den einen Tag, auf den es ankommt.
 *
 * Jetzt zeigt das Raster nur, ob und wie viel los ist, und der gewaehlte Tag
 * steht vollstaendig daneben. Diese Tests bewachen genau das: dass ein voller
 * Tag **alle** seine Touren zeigt, statt sie abzuschneiden.
 */
class CalendarControllerTest extends AbstractControllerTestCase
{
    private const JAHR = 2031;
    private const MONAT = 5;
    private const STADT = 'Kalenderprobe';

    /** @var array<int, Ride> */
    private array $angelegt = [];

    /**
     * Aufraeumen ueber eine Abfrage, nicht ueber die gemerkten Objekte: Der
     * Kernel wird zwischen den Tests neu gestartet, die Entities gehoeren dann
     * einem geschlossenen EntityManager. Ohne das trugen spaetere Tests die
     * Touren der frueheren mit sich herum — und zaehlten falsch.
     */
    protected function tearDown(): void
    {
        $this->angelegt = [];

        if (static::$booted) {
            $entityManager = static::getContainer()->get('doctrine')->getManager();

            $entityManager->createQuery('DELETE FROM App\\Entity\\Ride r WHERE r.title LIKE :muster')
                ->setParameter('muster', 'Probefahrt %')
                ->execute();

            $entityManager->createQuery('DELETE FROM App\\Entity\\City c WHERE c.city = :stadt')
                ->setParameter('stadt', self::STADT)
                ->execute();
        }

        parent::tearDown();
    }

    /**
     * Eine eigene Stadt fuer diese Tests.
     *
     * **Nicht** die erste aus den Fixtures: Das waere Hamburg, und dort
     * pruefen andere Tests, was auf der Stadtseite steht. Sechzig zusaetzliche
     * Touren dort haben NavigationTest umgeworfen — ein Fehlschlag, der
     * aussah, als haette der Kalender etwas kaputtgemacht, und der in
     * Wahrheit von diesem Test kam.
     */
    private function probestadt(EntityManagerInterface $entityManager): City
    {
        $vorhanden = $entityManager->getRepository(City::class)->findOneBy(['city' => self::STADT]);

        if (null !== $vorhanden) {
            return $vorhanden;
        }

        $stadt = new City();
        $stadt->setCity(self::STADT);
        $stadt->setTitle('Critical Mass ' . self::STADT);
        $stadt->setEnabled(true);
        $stadt->setLatitude(53.55);
        $stadt->setLongitude(9.99);
        $stadt->setTimezone('Europe/Berlin');

        $entityManager->persist($stadt);
        $entityManager->flush();

        return $stadt;
    }

    /**
     * Legt Touren an einem Tag des Probemonats an.
     *
     * Der Monat liegt bewusst weit in der Zukunft, damit die Fixtures nicht
     * hineinreichen und die Zahlen genau die sind, die dieser Test setzt.
     *
     * @return array<int, Ride>
     */
    private function tourenAnlegen(EntityManagerInterface $entityManager, int $tag, int $anzahl): array
    {
        $stadt = $this->probestadt($entityManager);

        $angelegt = [];

        for ($i = 0; $i < $anzahl; ++$i) {
            $tour = new Ride();
            $tour->setCity($stadt);
            $tour->setTitle(sprintf('Probefahrt %d/%d', $tag, $i));
            $tour->setDateTime(new \DateTime(sprintf('%04d-%02d-%02d 17:%02d:00', self::JAHR, self::MONAT, $tag, $i % 60)));
            $tour->setCreatedAt(new \DateTime());
            $tour->setUpdatedAt(new \DateTime());
            $tour->setEnabled(true);

            $entityManager->persist($tour);
            $angelegt[] = $tour;
        }

        $entityManager->flush();
        $this->angelegt = [...$this->angelegt, ...$angelegt];

        return $angelegt;
    }

    private function adresse(?int $tag = null): string
    {
        $adresse = sprintf('/calendar?year=%d&month=%d', self::JAHR, self::MONAT);

        return null === $tag ? $adresse : $adresse . '&day=' . $tag;
    }

    public function testTheMonthIsShownAsAGridOfDays(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->adresse());

        self::assertResponseIsSuccessful();
        self::assertSame(
            31,
            $crawler->filter('.calendar-month__day')->count(),
            'Mai hat 31 Tage, und jeder bekommt eine Zelle.'
        );
    }

    /**
     * Der Kern: Ein voller Tag zeigt **jede** seiner Touren.
     *
     * In der alten Ansicht steckten sie in einer 150 Pixel hohen Liste mit
     * `overflow: scroll` — sichtbar waren vielleicht sechs.
     */
    public function testABusyDayShowsEveryRideAndCutsNothingOff(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $this->tourenAnlegen($entityManager, 16, 60);

        $crawler = $client->request('GET', $this->adresse(16));

        self::assertResponseIsSuccessful();
        self::assertSame(
            60,
            $crawler->filter('.calendar-day__entry')->count(),
            'Alle 60 Touren stehen da — keine bleibt hinter einem Rollbalken.'
        );
        self::assertStringContainsString('60 Touren', $crawler->filter('.calendar-day__count')->text());
    }

    /**
     * Und das Raster kennzeichnet ihn, statt ihn wie jeden anderen zu zeigen.
     */
    public function testTheBusyDayIsMarkedInTheGrid(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $this->tourenAnlegen($entityManager, 16, 60);
        $this->tourenAnlegen($entityManager, 17, 1);

        $crawler = $client->request('GET', $this->adresse());

        self::assertSame(
            1,
            $crawler->filter('.calendar-month__day--busy')->count(),
            'Nur der volle Tag ist hervorgehoben, nicht der mit einer Tour.'
        );
        self::assertStringContainsString(
            '60 Touren',
            (string) $crawler->filter('.calendar-month__day--busy')->attr('aria-label'),
            'Die Zahl steht im Vorlesetext, nicht nur in Punkten.'
        );
    }

    /**
     * Ohne JavaScript muss es genauso gehen: Jeder Tag ist ein Verweis, und
     * der Server rendert den gewaehlten.
     */
    public function testEveryDayIsALinkThatWorksOnItsOwn(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $this->tourenAnlegen($entityManager, 9, 3);

        $crawler = $client->request('GET', $this->adresse());
        $zelle = $crawler->filter('.calendar-month__day')->eq(8); // der 9.

        $client->click($zelle->link());

        self::assertResponseIsSuccessful();
        self::assertSame(3, $client->getCrawler()->filter('.calendar-day__entry')->count());
    }

    public function testADayWithoutRidesSaysSo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->adresse(3));

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('keine Tour eingetragen', $crawler->filter('.calendar-day')->text());
        self::assertSame(0, $crawler->filter('.calendar-day__entry')->count());
    }

    /**
     * Ein unsinniger Monat fuehrt zum laufenden, nicht in einen Fehler.
     */
    public function testNonsenseMonthsFallBackInsteadOfBreaking(): void
    {
        $client = static::createClient();

        foreach (['year=2026&month=13', 'year=2026&month=0', 'year=1200&month=5', 'year=abc&month=xyz'] as $abfrage) {
            $client->request('GET', '/calendar?' . $abfrage);
            self::assertResponseIsSuccessful(sprintf('/calendar?%s wirft die Seite nicht um.', $abfrage));
        }
    }

    /**
     * Und ein Tag ausserhalb des Monats ebenso.
     */
    public function testADayOutsideTheMonthFallsBack(): void
    {
        $client = static::createClient();

        $client->request('GET', $this->adresse(99));

        self::assertResponseIsSuccessful();
        self::assertSame(1, $client->getCrawler()->filter('.calendar-day__heading')->count());
    }

    /**
     * Der Kopf nennt, was der Monat hergibt — sonst muss man zaehlen.
     */
    public function testTheHeadlineSaysHowMuchIsGoingOn(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $this->tourenAnlegen($entityManager, 4, 2);
        $this->tourenAnlegen($entityManager, 5, 3);

        $crawler = $client->request('GET', $this->adresse());
        $text = $crawler->filter('.calendar-page')->text();

        self::assertStringContainsString('5 Touren', $text);
        self::assertStringContainsString('2 Tagen', $text);
    }
}
