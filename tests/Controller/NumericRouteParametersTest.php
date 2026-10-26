<?php declare(strict_types=1);

namespace Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Ein Pfadteil, der eine Zahl sein soll, muss auch eine verlangen.
 *
 * Ohne Bedingung nimmt {id} alles entgegen, und der Wert landet unveraendert
 * in einer Abfrage gegen eine Integer-Spalte. Unter MySQL lieferte
 * `WHERE id = 'foo'` stillschweigend nichts und damit einen 404. **Unter
 * PostgreSQL wirft es** (SQLSTATE 22P02, „invalid input syntax for type
 * integer") und die Seite antwortet mit einem 500er.
 *
 * Aufgefallen ist es am 06.09.2026, einen Tag nach dem Umzug, an einer
 * einzelnen Anfrage des Yandex-Bots: `GET /norderstedt/2020-01-03/photo/foo`.
 *
 * Dieselbe Luecke, nur mit anderem Ausgang, hatte `/statistic/{year}/{month}`:
 * Dort kam die Zeichenkette bis in die Signatur
 * `listRidesAction(int $year, int $month)` und erzeugte einen TypeError —
 * 255 Mal.
 *
 * Und weil solche Adressen nun gar keine Route mehr treffen, kam eine zweite
 * Schwaeche ans Licht, die es laengst gab: die Schleife um
 * `remove_trailing_slash`, siehe testAnUnknownPathDoesNotLoop().
 */
class NumericRouteParametersTest extends AbstractControllerTestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function unsinnigeAdressen(): array
    {
        return [
            'die Anfrage des Bots' => ['/norderstedt/2020-01-03/photo/foo'],
            'Foto ohne Nummer' => ['/photo/foo'],
            'Foto halb Nummer' => ['/photo/12abc'],
            'Download ohne Nummer' => ['/photo/foo/download'],
            'Track ohne Nummer' => ['/track/download/foo'],
            'Statistik ohne Jahr' => ['/statistic/foo/bar'],
            'Statistik zu kurzes Jahr' => ['/statistic/20/9'],
            'Beitrag ohne Nummer' => ['/post/edit/foo'],
        ];
    }

    /**
     * Der Kern: keine dieser Adressen darf die Anwendung umwerfen, und keine
     * darf im Kreis laufen.
     */
    #[DataProvider('unsinnigeAdressen')]
    public function testSuchAnAddressNeitherBreaksNorLoops(string $adresse): void
    {
        $client = static::createClient();
        $client->followRedirects(false);

        [$status, $sprünge] = $this->verfolgen($client, $adresse);

        self::assertNotSame(500, $status, sprintf('%s wirft die Anwendung um.', $adresse));
        self::assertLessThan(5, $sprünge, sprintf('%s laeuft im Kreis.', $adresse));
    }

    /**
     * Und die Anfrage des Bots endet, wo sie hingehoert: im 404.
     */
    public function testTheBotsRequestEndsInANotFound(): void
    {
        $client = static::createClient();
        $client->followRedirects(false);

        [$status] = $this->verfolgen($client, '/norderstedt/2020-01-03/photo/foo');

        self::assertSame(404, $status);
    }

    /**
     * Die Schleife, die durch die Zahlbedingungen sichtbar wurde: Die Route
     * remove_trailing_slash passt auf jeden Pfad mit Schraegstrich am Ende,
     * weshalb Symfony fuer *jeden* unbekannten Pfad annahm, es gebe ihn mit
     * angehaengtem Schraegstrich — und der Controller schnitt ihn wieder ab.
     */
    public function testAnUnknownPathDoesNotLoop(): void
    {
        $client = static::createClient();
        $client->followRedirects(false);

        [$status, $sprünge] = $this->verfolgen($client, '/gibt/es/nicht');

        self::assertSame(404, $status, 'Was es nicht gibt, endet im 404.');
        self::assertLessThan(5, $sprünge, 'Und nicht in einer Schleife.');
    }

    /**
     * Der gute Fall bleibt unberuehrt: Eine echte Adresse mit Schraegstrich am
     * Ende wird weiterhin auf die saubere Form umgeleitet.
     */
    public function testARealAddressStillLosesItsTrailingSlash(): void
    {
        $client = static::createClient();
        $client->followRedirects(false);
        $client->request('GET', '/hamburg/');

        self::assertResponseRedirects();
        self::assertStringEndsWith(
            '/hamburg',
            (string) $client->getResponse()->headers->get('Location')
        );
    }

    /**
     * Und eine gueltige Zahl kommt weiterhin durch.
     */
    public function testANumericParameterStillReachesTheController(): void
    {
        $client = static::createClient();
        $client->request('GET', '/statistic/2026/9');

        self::assertResponseIsSuccessful();
    }

    /**
     * @return array{0: int, 1: int} Endstatus und Zahl der Spruenge
     */
    private function verfolgen(KernelBrowser $client, string $adresse): array
    {
        $client->request('GET', $adresse);
        $sprünge = 0;

        while ($client->getResponse()->isRedirection() && $sprünge < 10) {
            ++$sprünge;
            $client->followRedirect();
        }

        return [$client->getResponse()->getStatusCode(), $sprünge];
    }
}
