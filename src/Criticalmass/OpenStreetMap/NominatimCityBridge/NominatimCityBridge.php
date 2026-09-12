<?php declare(strict_types=1);

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use App\Entity\City;
use App\Entity\Region;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientException;

class NominatimCityBridge extends AbstractNominatimCityBridge
{
    /**
     * Schlaegt eine Stadt bei Nominatim nach -- hoechstens.
     *
     * Die Route /{citySlug} faengt **jeden** unbekannten Pfad ab. Wer
     * criticalmass.in/work-here aufruft, loest damit eine Anfrage an
     * OpenStreetMap aus, und ein Crawler, der Karriereseiten sucht, loest
     * hunderte aus. Am 12.09.2026 kam von dort dreimal ein 429 zurueck --
     * und weil die Ausnahme niemand auffing, wurde aus der Abfuhr eines
     * fremden Dienstes ein Serverfehler auf unserer Seite.
     *
     * Drei Dinge halten das jetzt in Schranken, und jedes fuer sich reicht,
     * damit die Seite steht:
     *
     *   Der Ratenbegrenzer deckelt, was **wir** nach draussen schicken, auf
     *   Nominatims Hoechstmass von einer Anfrage je Sekunde. Ist der Eimer
     *   leer, unterbleibt die Anfrage.
     *
     *   Der Zwischenspeicher merkt sich auch die leeren Antworten. Derselbe
     *   unsinnige Pfad fragt sonst jedes Mal neu nach.
     *
     *   Und scheitert es doch -- 429, Zeitueberschreitung, Wartung --, gibt
     *   es null statt einer Ausnahme. Die Vorlage kennt diesen Fall laengst
     *   und schreibt dann "wir konnten keine Stadt zu deiner Suche finden",
     *   was die ehrlichere Auskunft ist als ein 500er.
     */
    public function lookupCity(string $citySlug): ?City
    {
        $rohdaten = $this->rohdatenHolen($citySlug);

        return null === $rohdaten ? null : $this->createCity($rohdaten);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function rohdatenHolen(string $citySlug): ?array
    {
        // Der Schluessel muss die Anfrage eindeutig beschreiben und darf die
        // Zeichen nicht enthalten, die PSR-6 verbietet.
        $schluessel = 'nominatim_city_' . hash('xxh128', $citySlug);

        $gemerkt = $this->cache->get($schluessel, function () use ($citySlug): array|false {
            $antwort = $this->beiNominatimFragen($citySlug);

            // false statt null: Der Zwischenspeicher unterscheidet "nichts
            // gefunden" sonst nicht von "noch nicht gefragt" und liefe fuer
            // jeden unbekannten Pfad ins Leere.
            return $antwort ?? false;
        });

        return false === $gemerkt ? null : $gemerkt;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function beiNominatimFragen(string $citySlug): ?array
    {
        if (!$this->nominatimLimiter->create()->consume()->isAccepted()) {
            $this->logger->warning('Nominatim nicht gefragt: eigenes Kontingent erschoepft.', [
                'stadt' => $citySlug,
            ]);

            return null;
        }

        try {
            $response = $this->httpClient->request('GET', self::NOMINATIM_URL . 'search', [
                'query' => [
                    'city' => $citySlug,
                    'format' => 'json',
                    'addressdetails' => 1,
                ],
                'headers' => [
                    // Nominatim verlangt eine Kennung, unter der man erreichbar
                    // ist. "criticalmass.in/1.0" allein erfuellt das nicht.
                    'User-Agent' => 'criticalmass.in (https://criticalmass.in/)',
                ],
                'timeout' => 5,
            ]);

            $result = $response->toArray();
        } catch (HttpClientException $ausnahme) {
            $this->logger->warning('Nominatim hat nicht geantwortet.', [
                'stadt' => $citySlug,
                'grund' => $ausnahme->getMessage(),
            ]);

            return null;
        }

        $erster = array_shift($result);

        return is_array($erster) ? $erster : null;
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function createCity(array $result): ?City
    {
        $state = $result['address']['state'] ?? null;
        $region = $state !== null ? $this->doctrine->getRepository(Region::class)->findOneByName($state) : null;

        $cityName = $this->getCityNameFromResult($result);

        if (!$region || !$cityName) {
            return null;
        }

        $this->cityFactory
            ->withLatitude((float) $result['lat'])
            ->withLongitude((float) $result['lon'])
            ->withName($cityName)
            ->withTitle(sprintf('Critical Mass %s', $cityName))
            ->withRegion($region)
        ;

        return $this->cityFactory->build();
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function getCityNameFromResult(array $result): ?string
    {
        $propertyOrder = ['city', 'town', 'village', 'suburb'];

        foreach ($propertyOrder as $property) {
            if (array_key_exists($property, $result['address'])) {
                return $result['address'][$property];
            }
        }

        return null;
    }
}
