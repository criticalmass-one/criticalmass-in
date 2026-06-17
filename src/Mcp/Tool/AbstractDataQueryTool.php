<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Serializer\CriticalSerializerInterface;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Basis für Read-Tools, die die gefilterten Listen-Endpunkte der API spiegeln.
 * Wandelt die Tool-Argumente in eine DataQuery-Abfrage um (wie die API aus den
 * Query-Parametern) und serialisiert das Ergebnis mit den passenden Groups.
 */
abstract class AbstractDataQueryTool implements McpToolInterface
{
    public function __construct(
        protected readonly DataQueryManagerInterface $dataQueryManager,
        protected readonly CriticalSerializerInterface $serializer,
    ) {
    }

    /**
     * @return class-string
     */
    abstract protected function entityClass(): string;

    /**
     * Serialisierungs-Groups. Leeres Array bedeutet "kein Groups-Kontext"
     * (Default-Serialisierung, wie bei den gruppenlosen API-Endpunkten).
     *
     * @param array<string, mixed> $arguments
     *
     * @return list<string>
     */
    abstract protected function groups(array $arguments): array;

    public function call(array $arguments): string
    {
        $request = Request::create('/', 'GET', $this->toQueryParameters($arguments));

        try {
            $parameterList = RequestToListConverter::convert($request);
            $result = $this->dataQueryManager->query($parameterList, $this->entityClass());
        } catch (\Throwable $exception) {
            throw new McpToolException(sprintf('Abfrage fehlgeschlagen: %s', $exception->getMessage()));
        }

        $groups = $this->groups($arguments);
        $context = [] === $groups ? [] : ['groups' => $groups];

        return $this->serializer->serialize($result, 'json', $context);
    }

    /**
     * Gemeinsame Filter-/Sortier-/Paginierungs-Parameter der Listen-Endpunkte.
     *
     * @return array<string, mixed>
     */
    protected static function commonListProperties(): array
    {
        return [
            'orderBy' => ['type' => 'string', 'description' => 'Sortierfeld, z. B. "dateTime".'],
            'orderDirection' => ['type' => 'string', 'enum' => ['asc', 'desc'], 'description' => 'Sortierrichtung.'],
            'startValue' => ['type' => 'string', 'description' => 'Startwert für die Paginierung.'],
            'size' => ['type' => 'integer', 'description' => 'Maximale Anzahl Ergebnisse.'],
        ];
    }

    /**
     * Geografische Filter (Radius + Bounding Box).
     *
     * @return array<string, mixed>
     */
    protected static function geoProperties(): array
    {
        return [
            'centerLatitude' => ['type' => 'number', 'description' => 'Mittelpunkt-Breitengrad für Radius-Suche.'],
            'centerLongitude' => ['type' => 'number', 'description' => 'Mittelpunkt-Längengrad für Radius-Suche.'],
            'radius' => ['type' => 'number', 'description' => 'Suchradius in Kilometern.'],
            'bbNorthLatitude' => ['type' => 'number', 'description' => 'Bounding-Box: nördlicher Breitengrad.'],
            'bbSouthLatitude' => ['type' => 'number', 'description' => 'Bounding-Box: südlicher Breitengrad.'],
            'bbEastLongitude' => ['type' => 'number', 'description' => 'Bounding-Box: östlicher Längengrad.'],
            'bbWestLongitude' => ['type' => 'number', 'description' => 'Bounding-Box: westlicher Längengrad.'],
        ];
    }

    /**
     * Zeitliche Filter (Jahr/Monat/Tag).
     *
     * @return array<string, mixed>
     */
    protected static function dateProperties(): array
    {
        return [
            'year' => ['type' => 'integer', 'description' => 'Jahr (YYYY).'],
            'month' => ['type' => 'integer', 'description' => 'Monat (1–12, benötigt year).'],
            'day' => ['type' => 'integer', 'description' => 'Tag (1–31, benötigt year und month).'],
        ];
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, string>
     */
    private function toQueryParameters(array $arguments): array
    {
        $params = [];

        foreach ($arguments as $key => $value) {
            if (null === $value || '' === $value) {
                continue;
            }

            $params[$key] = match (true) {
                \is_array($value) => implode(',', $value),
                \is_bool($value) => $value ? 'true' : 'false',
                default => (string) $value,
            };
        }

        return $params;
    }
}
