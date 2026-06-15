<?php declare(strict_types=1);

namespace App\OAuth2;

/**
 * Kanonische Liste der OAuth2-/MCP-Scopes. Single Source of Truth für die
 * Discovery-Metadaten, die Dynamic Client Registration und die MCP-Tools.
 *
 * Muss mit `league_oauth2_server.scopes.available` in
 * config/packages/league_oauth2_server.yaml übereinstimmen
 * (abgesichert durch OAuthScopeTest).
 */
enum OAuthScope: string
{
    case RideRead = 'ride:read';
    case CityRead = 'city:read';
    case TrackRead = 'track:read';
    case PhotoRead = 'photo:read';
    case PostRead = 'post:read';
    case TrackWrite = 'track:write';
    case ParticipationWrite = 'participation:write';
    case EstimateWrite = 'estimate:write';
    case WeatherWrite = 'weather:write';

    public function label(): string
    {
        return match ($this) {
            self::RideRead => 'Termine und Rides lesen',
            self::CityRead => 'Städte, Orte und Cycles lesen',
            self::TrackRead => 'Tracks lesen',
            self::PhotoRead => 'Fotos lesen',
            self::PostRead => 'Forenbeiträge lesen',
            self::TrackWrite => 'Tracks erstellen und bearbeiten',
            self::ParticipationWrite => 'Teilnahme an Rides melden',
            self::EstimateWrite => 'Teilnehmerzahlen schätzen',
            self::WeatherWrite => 'Wetterdaten zu Rides melden',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $scope): string => $scope->value, self::cases());
    }

    /**
     * Filtert eine angefragte Scope-Liste auf die bekannten Scopes.
     *
     * @param list<string> $requested
     *
     * @return list<string>
     */
    public static function filterKnown(array $requested): array
    {
        return array_values(array_intersect($requested, self::values()));
    }
}
