<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\City;
use App\Entity\Ride;
use App\Enum\RideTypeEnum;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Die Übersichtskarte.
 *
 * Bewacht vor allem eines: dass „Tag“ der Tag am Ort der Tour ist. Eine
 * Abendtour an der amerikanischen Westkueste liegt in UTC schon im Folgetag —
 * wer nach UTC schneidet, zeigt sie am falschen Datum.
 */
class RideMapControllerTest extends AbstractControllerTestCase
{
    private const PRAEFIX = 'Kartenprobe';

    protected function tearDown(): void
    {
        if (static::$booted) {
            $entityManager = static::getContainer()->get('doctrine')->getManager();

            $entityManager->createQuery('DELETE FROM App\\Entity\\Ride r WHERE r.title LIKE :muster')
                ->setParameter('muster', self::PRAEFIX . ' %')
                ->execute();

            $entityManager->createQuery('DELETE FROM App\\Entity\\City c WHERE c.city LIKE :muster')
                ->setParameter('muster', self::PRAEFIX . ' %')
                ->execute();
        }

        parent::tearDown();
    }

    private function entityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    private function stadt(string $name, string $zeitzone, float $breite, float $laenge): City
    {
        $stadt = new City();
        $stadt->setCity(self::PRAEFIX . ' ' . $name);
        $stadt->setTitle('Critical Mass ' . $name);
        $stadt->setEnabled(true);
        $stadt->setLatitude($breite);
        $stadt->setLongitude($laenge);
        $stadt->setTimezone($zeitzone);

        $this->entityManager()->persist($stadt);

        return $stadt;
    }

    private function tour(City $stadt, string $name, string $utc): Ride
    {
        $tour = new Ride();
        $tour->setCity($stadt);
        $tour->setTitle(self::PRAEFIX . ' ' . $name);
        $tour->setDateTime(new \DateTime($utc, new \DateTimeZone('UTC')));
        $tour->setCreatedAt(new \DateTime());
        $tour->setUpdatedAt(new \DateTime());
        $tour->setEnabled(true);

        $this->entityManager()->persist($tour);

        return $tour;
    }

    /** @return array<string, array<string, mixed>> Titel (ohne Praefix) => Eintrag */
    private function eintraege(string $datum, ?string $bis = null): array
    {
        $client = static::getClient();
        $client->request('GET', '/map/rides/' . $datum . (null === $bis ? '' : '/' . $bis));

        self::assertResponseIsSuccessful();

        $daten = json_decode((string) $client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame($datum, $daten['date']);

        $eigene = [];

        foreach ($daten['rides'] as $eintrag) {
            if (str_starts_with((string) $eintrag['title'], self::PRAEFIX . ' ')) {
                $eigene[substr($eintrag['title'], strlen(self::PRAEFIX) + 1)] = $eintrag;
            }
        }

        return $eigene;
    }

    public function testThePageShowsTheFiltersForTheRequestedDay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/map?date=2031-05-09&q=Hamburg&active=0&type[]=KIDICAL_MASS');

        self::assertResponseIsSuccessful();
        self::assertSame('2031-05-09', $crawler->filter('input[name="date"]')->attr('value'));
        self::assertSame('Hamburg', $crawler->filter('input[name="q"]')->attr('value'));
        self::assertNull($crawler->filter('input[name="active"]')->attr('checked'));
        self::assertCount(count(RideTypeEnum::cases()), $crawler->filter('input[name="type[]"]'));
        self::assertSame(
            ['KIDICAL_MASS'],
            $crawler->filter('input[name="type[]"][checked]')->extract(['value'])
        );
        self::assertCount(1, $crawler->filter('[data-controller="map--day-map"]'));
    }

    public function testOnlyActiveCitiesIsTheDefault(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/map');

        self::assertResponseIsSuccessful();
        self::assertNotNull($crawler->filter('input[name="active"]')->attr('checked'));
        self::assertSame(date('Y-m-d'), $crawler->filter('input[name="date"]')->attr('value'));
    }

    public function testNonsenseInTheAddressFallsBackToToday(): void
    {
        $client = static::createClient();

        foreach (['date=abc', 'date=2031-02-31', 'date[]=x', 'q[]=x&type=unsinn'] as $unsinn) {
            $crawler = $client->request('GET', '/map?' . $unsinn);

            self::assertResponseIsSuccessful($unsinn);
            self::assertSame(date('Y-m-d'), $crawler->filter('input[name="date"]')->attr('value'), $unsinn);
        }
    }

    public function testADateThatDoesNotExistIsNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/map/rides/2031-02-31');
        self::assertResponseStatusCodeSame(404);
    }

    public function testARideBelongsToItsLocalDay(): void
    {
        static::createClient();

        // 03:00 UTC am 10. Mai ist in Portland der 9. Mai, 20:00 Uhr.
        $this->tour($this->stadt('Portland', 'America/Los_Angeles', 45.52, -122.68), 'Abend', '2031-05-10 03:00:00');
        // 23:30 UTC am 8. Mai ist in Jakarta der 9. Mai, 06:30 Uhr.
        $this->tour($this->stadt('Jakarta', 'Asia/Jakarta', -6.2, 106.8), 'Morgen', '2031-05-08 23:30:00');
        $this->tour($this->stadt('Berlin', 'Europe/Berlin', 52.52, 13.4), 'Feierabend', '2031-05-09 17:00:00');
        $this->entityManager()->flush();

        $neunter = $this->eintraege('2031-05-09');

        self::assertSame(['Abend', 'Feierabend', 'Morgen'], $this->sortiert(array_keys($neunter)));
        self::assertSame('20:00', $neunter['Abend']['time']);
        self::assertSame('06:30', $neunter['Morgen']['time']);
        self::assertSame('19:00', $neunter['Feierabend']['time']);

        self::assertSame([], $this->eintraege('2031-05-10'));
        self::assertSame([], $this->eintraege('2031-05-08'));
    }

    public function testAPeriodCoversItsFirstAndLastLocalDay(): void
    {
        static::createClient();

        $berlin = $this->stadt('Zeitraum', 'Europe/Berlin', 52.52, 13.4);
        $portland = $this->stadt('Westen', 'America/Los_Angeles', 45.52, -122.68);

        $this->tour($berlin, 'davor', '2031-08-03 17:00:00');
        $this->tour($berlin, 'Montag', '2031-08-04 17:00:00');
        // Sonntagabend in Portland ist in UTC schon Montag der Folgewoche.
        $this->tour($portland, 'Sonntag', '2031-08-11 03:00:00');
        $this->tour($berlin, 'danach', '2031-08-11 17:00:00');
        $this->entityManager()->flush();

        $woche = $this->eintraege('2031-08-04', '2031-08-10');

        self::assertSame(['Montag', 'Sonntag'], $this->sortiert(array_keys($woche)));
        self::assertSame('2031-08-04', $woche['Montag']['date']);
        self::assertSame('2031-08-10', $woche['Sonntag']['date']);

        self::assertCount(4, $this->eintraege('2031-08-01', '2031-08-31'));
    }

    public function testAPeriodIsAtMostAMonthAndRunsForward(): void
    {
        $client = static::createClient();

        $client->request('GET', '/map/rides/2031-08-01/2031-08-31');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/map/rides/2031-08-01/2031-09-01');
        self::assertResponseStatusCodeSame(404);

        $client->request('GET', '/map/rides/2031-08-10/2031-08-04');
        self::assertResponseStatusCodeSame(404);
    }

    public function testThePeriodIsChosenFromTheAddress(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/map?date=2031-05-09&range=week');
        self::assertSame('week', $crawler->filter('select[name="range"] option[selected]')->attr('value'));

        $crawler = $client->request('GET', '/map?range=jahrzehnt');
        self::assertResponseIsSuccessful();
        self::assertSame('day', $crawler->filter('select[name="range"] option[selected]')->attr('value'));
    }

    public function testAnEntryCarriesWhatTheMapNeeds(): void
    {
        static::createClient();

        $stadt = $this->stadt('Treffpunkt', 'Europe/Berlin', 53.55, 9.99);

        $mitTreffpunkt = $this->tour($stadt, 'mit', '2031-06-13 17:00:00');
        $mitTreffpunkt->setLatitude(53.5);
        $mitTreffpunkt->setLongitude(10.1);
        $mitTreffpunkt->setLocation('Rathausmarkt');
        $mitTreffpunkt->setRideType(RideTypeEnum::KIDICAL_MASS);
        $mitTreffpunkt->setSlug('kartenprobe-mit');
        $mitTreffpunkt->setDescription("Wir fahren **gemütlich** zum <em>Elbstrand</em>.\nBringt Licht mit.");

        $abgesagt = $this->tour($stadt, 'abgesagt', '2031-06-13 18:00:00');
        $abgesagt->setEnabled(false);
        $abgesagt->setSlug('kartenprobe-abgesagt');

        $this->entityManager()->flush();

        $eintraege = $this->eintraege('2031-06-13');

        self::assertEqualsWithDelta(53.5, $eintraege['mit']['latitude'], 0.0001);
        self::assertEqualsWithDelta(10.1, $eintraege['mit']['longitude'], 0.0001);
        self::assertSame('Rathausmarkt', $eintraege['mit']['location']);
        self::assertSame('KIDICAL_MASS', $eintraege['mit']['type']);
        self::assertSame(self::PRAEFIX . ' Treffpunkt', $eintraege['mit']['city']);
        self::assertTrue($eintraege['mit']['active']);
        self::assertFalse($eintraege['mit']['cancelled']);

        // Der Suchtext traegt alles, wonach sich die Tour finden lassen soll — ohne Auszeichnung.
        foreach (['Kartenprobe Treffpunkt', 'Kartenprobe mit', 'Rathausmarkt', 'Elbstrand', 'Bringt Licht mit', 'Kidical Mass'] as $wort) {
            self::assertStringContainsString($wort, $eintraege['mit']['text']);
        }
        self::assertStringNotContainsString('<em>', $eintraege['mit']['text']);

        // Ohne Treffpunkt steht die Tour in der Stadtmitte statt gar nicht.
        self::assertEqualsWithDelta(53.55, $eintraege['abgesagt']['latitude'], 0.0001);
        self::assertEqualsWithDelta(9.99, $eintraege['abgesagt']['longitude'], 0.0001);
        self::assertTrue($eintraege['abgesagt']['cancelled']);
        // Touren aus den Zyklen tragen keine Art; sie zaehlen als Critical Mass.
        self::assertSame('CRITICAL_MASS', $eintraege['abgesagt']['type']);
    }

    public function testDormantCitiesAreMarkedAndDisabledCitiesAreLeftOut(): void
    {
        static::createClient();

        $ruhend = $this->stadt('Ruhend', 'Europe/Berlin', 50.0, 8.0);
        $ruhend->setActivityScore(0.0);
        $ruhend->setCreatedAt(new \DateTime('-2 years'));
        $this->tour($ruhend, 'ruhend', '2031-07-11 17:00:00');

        $aus = $this->stadt('Aus', 'Europe/Berlin', 51.0, 9.0);
        $aus->setEnabled(false);
        $this->tour($aus, 'aus', '2031-07-11 17:00:00');

        $this->entityManager()->flush();

        $eintraege = $this->eintraege('2031-07-11');

        self::assertSame(['ruhend'], array_keys($eintraege));
        self::assertFalse($eintraege['ruhend']['active']);
    }

    public function testTheCalendarLinksToTheMapOfTheChosenDay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/calendar?year=2031&month=5&day=9');

        self::assertResponseIsSuccessful();
        self::assertSame('/map?date=2031-05-09', $crawler->filter('a.calendar-day__map')->attr('href'));
    }

    /**
     * @param list<string> $werte
     *
     * @return list<string>
     */
    private function sortiert(array $werte): array
    {
        sort($werte);

        return $werte;
    }
}
