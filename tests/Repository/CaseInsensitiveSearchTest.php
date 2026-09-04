<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Criticalmass\DataQuery\Query\TitleQuery;
use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\PostRepository;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die Suche muss unabhaengig von der Gross- und Kleinschreibung finden.
 *
 * Unter MySQL ist das geschenkt: Die Kollation vergleicht ohnehin schreibungs-
 * blind, ein reiner Verhaltenstest bestuende hier also auch dann, wenn im Code
 * gar kein LOWER() staende. PostgreSQL vergleicht LIKE dagegen schreibungs-
 * empfindlich — eine Suche nach "hamburg" faende dort kein "Hamburg", ohne
 * Fehlermeldung und ohne Eintrag im Protokoll.
 *
 * Deshalb pruefen die ersten Tests die erzeugte Abfrage selbst. Das ist die
 * einzige Zusicherung, die auf MySQL etwas aussagt. Die Verhaltenstests danach
 * sichern ab, dass die Suche ueber der Umstellung nicht kaputtgegangen ist.
 */
class CaseInsensitiveSearchTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    private function postRepository(): PostRepository
    {
        $repository = static::getContainer()->get(PostRepository::class);
        self::assertInstanceOf(PostRepository::class, $repository);

        return $repository;
    }

    private function cityRepository(): CityRepository
    {
        $repository = static::getContainer()->get(CityRepository::class);
        self::assertInstanceOf(CityRepository::class, $repository);

        return $repository;
    }

    private function rideRepository(): RideRepository
    {
        $repository = static::getContainer()->get(RideRepository::class);
        self::assertInstanceOf(RideRepository::class, $repository);

        return $repository;
    }

    public function testForumSearchComparesLowercaseOnBothSides(): void
    {
        $query = $this->postRepository()->querySearchInForum('Fahrrad');
        $dql = $query->getDQL();

        self::assertStringContainsString('LOWER(p.message)', $dql);
        self::assertStringContainsString('LOWER(t.title)', $dql);

        $parameter = $query->getParameter('term');
        self::assertNotNull($parameter);
        self::assertSame('%fahrrad%', $parameter->getValue());
    }

    public function testForumSearchLowersTheSearchTerm(): void
    {
        $parameter = $this->postRepository()->querySearchInForum('FAHRRAD')->getParameter('term');

        self::assertNotNull($parameter);
        self::assertSame('%fahrrad%', $parameter->getValue(), 'Der Suchbegriff geht kleingeschrieben in die Abfrage.');
    }

    public function testForumSearchStillEscapesWildcards(): void
    {
        $parameter = $this->postRepository()->querySearchInForum('100%')->getParameter('term');

        self::assertNotNull($parameter);
        self::assertSame('%100\%%', $parameter->getValue(), 'Das Kleinschreiben darf die Maskierung von % nicht aushebeln.');
    }

    public function testApiTitleQueryComparesLowercaseOnBothSides(): void
    {
        $titleQuery = new TitleQuery();
        $titleQuery->setTitle('Kidical');

        $builder = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(\App\Entity\Ride::class, 'r');

        $dql = $titleQuery->createOrmQuery($builder)->getDQL();

        self::assertStringContainsString('LOWER(r.title)', $dql);
    }

    public function testApiTitleQueryLowersTheSearchTerm(): void
    {
        $titleQuery = new TitleQuery();
        $titleQuery->setTitle('KIDICAL');

        $builder = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(\App\Entity\Ride::class, 'r');

        $parameter = $titleQuery->createOrmQuery($builder)->getQuery()->getParameter('title');

        self::assertNotNull($parameter);
        self::assertSame('%kidical%', $parameter->getValue());
    }

    /**
     * @return list<array{0: string}>
     */
    public static function schreibweisen(): array
    {
        return [['Hamburg'], ['hamburg'], ['HAMBURG'], ['hAmBuRg']];
    }

    #[DataProvider('schreibweisen')]
    public function testCitySearchFindsHamburgInAnyCase(string $term): void
    {
        $treffer = $this->cityRepository()->searchByQuery($term);
        $namen = array_map(static fn (City $city): string => (string) $city->getCity(), $treffer);

        self::assertContains('Hamburg', $namen, sprintf('Die Suche nach "%s" findet Hamburg nicht.', $term));
    }

    public function testCitySearchStillReturnsNothingForNonsense(): void
    {
        self::assertSame([], $this->cityRepository()->searchByQuery('gibtesnicht' . uniqid()));
    }

    public function testRideSearchAcceptsMixedCaseWithoutFailing(): void
    {
        // Der Bestand der Fixtures ist hier nicht festgelegt; gesichert wird, dass
        // die Abfrage laeuft und beide Schreibweisen dasselbe liefern.
        $gross = $this->rideRepository()->searchByQuery('CRITICAL');
        $klein = $this->rideRepository()->searchByQuery('critical');

        self::assertSame(count($gross), count($klein), 'Gross- und Kleinschreibung liefern dieselbe Trefferzahl.');
    }
}
