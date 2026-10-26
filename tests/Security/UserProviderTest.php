<?php declare(strict_types=1);

namespace Tests\Security;

use App\Entity\User;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Die Anmeldung muss finden, wer gemeint ist — auch bei anderer Schreibung.
 *
 * MySQL vergleicht Text ueber die Kollation schreibungsblind, PostgreSQL nicht.
 * Ohne die schreibungsblinde Suche kaeme nach dem Plattformwechsel niemand mehr
 * hinein, der seinen Namen anders schreibt als er gespeichert ist; auf der
 * Produktion tragen 3981 von 21140 Benutzernamen Grossbuchstaben.
 *
 * Genauso wichtig ist die Gegenrichtung: Sobald zwei Konten sich nur in der
 * Schreibung unterscheiden, darf die Anmeldung nicht raten.
 */
class UserProviderTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    /** @var UserProviderInterface<User> */
    private UserProviderInterface $provider;

    /** @var list<int> */
    private array $angelegte = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        $provider = static::getContainer()->get('App\Security\UserProvider\UserProvider');
        self::assertInstanceOf(UserProviderInterface::class, $provider);
        $this->provider = $provider;
    }

    protected function tearDown(): void
    {
        foreach ($this->angelegte as $id) {
            $user = $this->entityManager->find(User::class, $id);

            if ($user instanceof User) {
                $this->entityManager->remove($user);
            }
        }

        $this->entityManager->flush();
        $this->angelegte = [];

        parent::tearDown();
    }

    private function nurAufPostgreSql(): void
    {
        $plattform = $this->entityManager->getConnection()->getDatabasePlatform();

        if (!$plattform instanceof PostgreSQLPlatform) {
            self::markTestSkipped(
                'Nur unter PostgreSQL aussagekraeftig: MySQL vergleicht Text ueber die Kollation '
                . 'ohnehin schreibungsblind und entscheidet die Mehrdeutigkeit selbst.'
            );
        }
    }

    private function anlegen(string $username): User
    {
        $user = (new User())
            ->setUsername($username)
            ->setEmail($username . '@beispiel.test');
        $user->setEnabled(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->angelegte[] = (int) $user->getId();

        return $user;
    }

    public function testExactSpellingIsFound(): void
    {
        $user = $this->provider->loadUserByIdentifier('testuser');

        self::assertInstanceOf(User::class, $user);
        self::assertSame('testuser', $user->getUsername());
    }

    public function testDifferentSpellingIsFoundToo(): void
    {
        foreach (['TestUser', 'TESTUSER', 'tEsTuSeR'] as $schreibweise) {
            $user = $this->provider->loadUserByIdentifier($schreibweise);

            self::assertInstanceOf(User::class, $user);
            self::assertSame('testuser', $user->getUsername(), sprintf('"%s" fuehrt zum Konto "testuser".', $schreibweise));
        }
    }

    public function testUnknownIdentifierIsStillRejected(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->provider->loadUserByIdentifier('gibtesnicht' . uniqid());
    }

    /**
     * Nur unter PostgreSQL eine Aussage: Dort ist der erste, genaue Vergleich
     * wirklich genau. Unter MySQL vergleicht schon findOneBy ueber die
     * Kollation schreibungsblind — dort loest die Datenbank die Mehrdeutigkeit
     * seit jeher selbst auf, willkuerlich und unabhaengig von dieser Klasse.
     */
    public function testExactMatchWinsOverASpellingVariant(): void
    {
        $this->nurAufPostgreSql();

        $this->anlegen('Doppelgaenger');
        $this->anlegen('doppelgaenger');

        foreach (['Doppelgaenger', 'doppelgaenger'] as $schreibweise) {
            $gefunden = $this->provider->loadUserByIdentifier($schreibweise);

            self::assertInstanceOf(User::class, $gefunden);
            self::assertSame($schreibweise, $gefunden->getUsername(), 'Der genaue Treffer hat Vorrang.');
        }
    }

    /**
     * Gibt es zwei Schreibvarianten und keinen genauen Treffer, ist nicht
     * entschieden, wer gemeint ist. Dann darf niemand hinein.
     *
     * Ebenfalls nur unter PostgreSQL pruefbar, aus demselben Grund.
     */
    public function testAmbiguousSpellingLetsNobodyIn(): void
    {
        $this->nurAufPostgreSql();

        $this->anlegen('Zwilling');
        $this->anlegen('zwilling');

        $this->expectException(UserNotFoundException::class);

        $this->provider->loadUserByIdentifier('ZWILLING');
    }
}
