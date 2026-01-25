<?php declare(strict_types=1);

namespace Tests\Controller\Api\Schema;

/**
 * Central definitions of expected API response schemas.
 *
 * These schemas document the API output structure after switching to Symfony Serializer.
 *
 * Notes:
 * - All property names use snake_case (CamelCaseToSnakeCaseNameConverter)
 * - All DateTime fields are Unix timestamps (int) for API backwards compatibility
 * - Nullable fields use 'type|null' syntax
 * - Optional fields use 'field?' key suffix
 */
final class ApiSchemaDefinitions
{
    // =========================================================================
    // City Schemas
    // =========================================================================

    /**
     * Schema for City list endpoint: GET /api/city
     * Actual keys: id, main_slug, name, title, latitude, longitude, slugs, city_population, timezone
     */
    public const CITY_LIST_ITEM_SCHEMA = [
        'id' => 'int',
        'main_slug' => 'array',
        'name' => 'string',
        'title' => 'string',
        'latitude' => 'float',
        'longitude' => 'float',
        'slugs' => 'array',
        'city_population' => 'int|null',
        'timezone' => 'string',
    ];

    /**
     * Schema for nested CitySlug objects within City
     */
    public const CITY_SLUG_SCHEMA = [
        'id' => 'int',
        'slug' => 'string',
    ];

    /**
     * Schema for City detail endpoint: GET /api/{citySlug}
     */
    public const CITY_DETAIL_SCHEMA = self::CITY_LIST_ITEM_SCHEMA;

    // =========================================================================
    // Ride Schemas
    // =========================================================================

    /**
     * Schema for Ride list endpoint: GET /api/ride (ride-list group)
     * Actual keys: id, title, date_time, location, latitude, longitude, estimated_participants, views, enabled
     */
    public const RIDE_LIST_ITEM_SCHEMA = [
        'id' => 'int',
        'title' => 'string',
        'date_time' => 'int', // Unix timestamp (Ride uses DateTime<'U'>)
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'views' => 'int',
        'enabled' => 'bool',
        // Optional fields
        'estimated_participants?' => 'int|null',
    ];

    /**
     * Schema for Ride detail endpoint: GET /api/{citySlug}/{rideIdentifier}
     * Extended ride list with additional relations
     */
    public const RIDE_DETAIL_SCHEMA = [
        'id' => 'int',
        'title' => 'string',
        'date_time' => 'int', // Unix timestamp (Ride uses DateTime<'U'>)
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'views' => 'int',
        'participations_number_yes' => 'int',
        'participations_number_maybe' => 'int',
        'participations_number_no' => 'int',
        'enabled' => 'bool',
        // Extended relations (always present in detail view)
        'cycle' => 'array', // CityCycle object
        'city' => 'array', // City object
        'social_network_profiles' => 'array',
        // Optional fields
        'estimated_participants?' => 'int|null',
    ];

    // =========================================================================
    // Photo Schemas
    // =========================================================================

    /**
     * Schema for Photo list endpoint: GET /api/photo
     * Actual keys: id, latitude, longitude, description, views, creation_date_time, image_name, updated_at, location, exif_creation_date
     */
    public const PHOTO_SCHEMA = [
        'id' => 'int',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'description' => 'string|null',
        'views' => 'int',
        'creation_date_time' => 'int', // Unix timestamp
        'image_name' => 'string',
        'updated_at' => 'int|null', // Unix timestamp or null
        'location' => 'string|null',
        'exif_creation_date' => 'int|null', // Unix timestamp or null
    ];

    // =========================================================================
    // Track Schemas
    // =========================================================================

    /**
     * Schema for Track list endpoint: GET /api/track (api-public group)
     * Actual keys: id, creation_date_time, start_date_time, end_date_time, distance, points, polylineString
     */
    public const TRACK_PUBLIC_SCHEMA = [
        'id' => 'int',
        'creation_date_time' => 'int', // Unix timestamp
        'start_date_time' => 'int|null', // Unix timestamp
        'end_date_time' => 'int|null', // Unix timestamp
        'distance' => 'float|int|null', // Can be int or float
        'points' => 'int|null',
        'polylineString' => 'string|null', // Note: camelCase here
    ];

    /**
     * Schema for Track detail with user info (api-private group)
     */
    public const TRACK_PRIVATE_SCHEMA = [
        'id' => 'int',
        'username' => 'string|null',
        'creation_date_time' => 'int', // Unix timestamp
        'start_date_time' => 'int|null',
        'end_date_time' => 'int|null',
        'distance' => 'float|int|null',
        'points' => 'int|null',
        'polylineString' => 'string|null',
        'user?' => 'array', // User object
    ];

    /**
     * Schema for Track in timelapse context
     */
    public const TRACK_TIMELAPSE_SCHEMA = [
        'id' => 'int',
        'username' => 'string|null',
        'user?' => 'array',
        'creation_date_time' => 'int', // Unix timestamp
        'start_date_time' => 'int|null',
        'end_date_time' => 'int|null',
        'distance' => 'float|int|null',
        'points' => 'int|null',
        'polylineString' => 'string|null',
        'color_red' => 'int',
        'color_green' => 'int',
        'color_blue' => 'int',
    ];

    // =========================================================================
    // Location Schemas
    // =========================================================================

    /**
     * Schema for Location endpoint: GET /api/{citySlug}/location
     */
    public const LOCATION_SCHEMA = [
        'id' => 'int',
        'slug' => 'string',
        'latitude' => 'float',
        'longitude' => 'float',
        'title' => 'string',
        'description' => 'string|null',
    ];

    // =========================================================================
    // CityCycle Schemas
    // =========================================================================

    /**
     * Schema for CityCycle list: GET /api/cycles
     * Actual keys: id, city, day_of_week, week_of_month, time, location, latitude, longitude, created_at, valid_from
     */
    public const CYCLE_SCHEMA = [
        'id' => 'int',
        'city' => 'array', // City object
        'day_of_week' => 'int',
        'week_of_month' => 'int|null',
        'time' => 'int|null', // Unix timestamp
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'created_at' => 'int', // Unix timestamp
        'valid_from' => 'int|null', // Unix timestamp
    ];

    // =========================================================================
    // Subride Schemas
    // =========================================================================

    /**
     * Schema for Subride list: GET /api/{citySlug}/{rideIdentifier}/subride
     */
    public const SUBRIDE_SCHEMA = [
        'id' => 'int',
        'title' => 'string',
        'description' => 'string|null',
        'timestamp' => 'int', // Virtual property: Unix timestamp from dateTime
        'date_time' => 'int|null', // Unix timestamp
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'created_at' => 'int', // Unix timestamp
        'updated_at' => 'int|null',
    ];

    /**
     * Schema for Subride detail with ride info (extended-subride-list group)
     */
    public const SUBRIDE_EXTENDED_SCHEMA = [
        'id' => 'int',
        'title' => 'string',
        'description' => 'string|null',
        'timestamp' => 'int',
        'date_time' => 'int|null', // Unix timestamp
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'created_at' => 'int', // Unix timestamp
        'updated_at' => 'int|null',
        'ride' => 'array', // Ride object
    ];

    // =========================================================================
    // SocialNetworkProfile Schemas
    // =========================================================================

    /**
     * Schema for SocialNetworkProfile (ride-list group)
     */
    public const SOCIAL_NETWORK_PROFILE_SCHEMA = [
        'id' => 'int',
        'identifier' => 'string',
        'network' => 'string',
        'created_at' => 'int|null', // Unix timestamp
        'auto_fetch' => 'bool',
        'auto_publish' => 'bool',
    ];

    // =========================================================================
    // SocialNetworkFeedItem Schemas
    // =========================================================================

    /**
     * Schema for SocialNetworkFeedItem
     */
    public const SOCIAL_NETWORK_FEED_ITEM_SCHEMA = [
        'id' => 'int',
        'unique_identifier' => 'string',
        'permalink' => 'string|null',
        'title' => 'string|null',
        'text' => 'string',
        'date_time' => 'int', // Unix timestamp
        'hidden' => 'bool',
        'deleted' => 'bool',
        'created_at' => 'int', // Unix timestamp
        'raw' => 'string|null',
    ];

    // =========================================================================
    // Weather Schemas
    // =========================================================================

    /**
     * Schema for Weather object
     */
    public const WEATHER_SCHEMA = [
        'id' => 'int',
        'weather_date_time' => 'int', // Unix timestamp
        'creation_date_time' => 'int', // Unix timestamp
        'temperature_min' => 'float',
        'temperature_max' => 'float',
        'temperature_morning' => 'float',
        'temperature_day' => 'float',
        'temperature_evening' => 'float',
        'temperature_night' => 'float',
        'pressure' => 'float',
        'humidity' => 'float',
        'weather_code' => 'int',
        'weather' => 'string',
        'weather_description' => 'string',
        'weather_icon' => 'string',
        'wind_speed' => 'float',
        'wind_direction' => 'float',
        'clouds' => 'float',
        'precipitation' => 'float',
    ];

    // =========================================================================
    // Estimate Schemas
    // =========================================================================

    /**
     * Schema for estimate response after POST /api/estimate
     * Note: RideEstimate is not directly serialized, only the Ride is returned
     */
    public const ESTIMATE_RESPONSE_SCHEMA = self::RIDE_DETAIL_SCHEMA;

    // =========================================================================
    // Helper: Minimal schemas for nested object validation
    // =========================================================================

    /**
     * Minimal City schema for nested validation (when city appears in other responses)
     */
    public const CITY_NESTED_MINIMAL_SCHEMA = [
        'name' => 'string',
        'slug' => 'string',
    ];

    /**
     * Minimal User schema for nested validation
     */
    public const USER_MINIMAL_SCHEMA = [
        'id' => 'int',
        'username' => 'string|null',
    ];
}
