<?php declare(strict_types=1);

namespace Tests\Controller\Api\Schema;

/**
 * Central definitions of expected API response schemas.
 *
 * These schemas document the current JMS Serializer output structure
 * and will be used to verify compatibility after switching to Symfony Serializer.
 *
 * Notes:
 * - All property names are lowercase (JMS lowercase naming strategy)
 * - DateTime with 'U' format = Unix timestamp (int)
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
     */
    public const CITY_LIST_ITEM_SCHEMA = [
        'slug' => 'string',
        'color' => [
            'red' => 'int',
            'green' => 'int',
            'blue' => 'int',
        ],
        'mainslug' => [
            'id' => 'int',
            'slug' => 'string',
        ],
        'name' => 'string',
        'title' => 'string',
        'description' => 'string|null',
        'latitude' => 'float',
        'longitude' => 'float',
        'slugs' => 'array',
        'socialnetworkprofiles' => 'array',
        'citypopulation' => 'int|null',
        'punchline' => 'string|null',
        'longdescription' => 'string|null',
        'timezone' => 'string',
        'threadnumber' => 'int',
        'postnumber' => 'int',
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
     * Same as list item but may include additional relations
     */
    public const CITY_DETAIL_SCHEMA = self::CITY_LIST_ITEM_SCHEMA;

    // =========================================================================
    // Ride Schemas
    // =========================================================================

    /**
     * Schema for Ride list endpoint: GET /api/ride (ride-list group)
     */
    public const RIDE_LIST_ITEM_SCHEMA = [
        'id' => 'int',
        'slug' => 'string|null',
        'title' => 'string',
        'description' => 'string|null',
        'datetime' => 'int', // Unix timestamp
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'estimatedparticipants' => 'int|null',
        'estimateddistance' => 'float|null',
        'estimatedduration' => 'float|null',
        'views' => 'int',
        'participationsnumberyes' => 'int',
        'participationsnumbermaybe' => 'int',
        'participationsnumberno' => 'int',
        'enabled' => 'bool',
        'disabledreason' => 'string|null',
        'ridetype' => 'string|null',
        'disabledreasonmessage' => 'string|null',
    ];

    /**
     * Schema for Ride detail endpoint: GET /api/{citySlug}/{rideIdentifier}
     * Extended ride list with additional relations
     */
    public const RIDE_DETAIL_SCHEMA = [
        'id' => 'int',
        'slug' => 'string|null',
        'title' => 'string',
        'description' => 'string|null',
        'datetime' => 'int', // Unix timestamp
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'estimatedparticipants' => 'int|null',
        'estimateddistance' => 'float|null',
        'estimatedduration' => 'float|null',
        'views' => 'int',
        'participationsnumberyes' => 'int',
        'participationsnumbermaybe' => 'int',
        'participationsnumberno' => 'int',
        'enabled' => 'bool',
        'disabledreason' => 'string|null',
        'ridetype' => 'string|null',
        'disabledreasonmessage' => 'string|null',
        // Extended relations (may be present)
        'cycle?' => 'array', // CityCycle object
        'city?' => 'array', // City object
        'tracks?' => 'array',
        'subrides?' => 'array',
        'posts?' => 'array',
        'photos?' => 'array',
        'socialnetworkprofiles?' => 'array',
        'weather?' => 'array', // Weather object
    ];

    // =========================================================================
    // Photo Schemas
    // =========================================================================

    /**
     * Schema for Photo list endpoint: GET /api/photo
     */
    public const PHOTO_SCHEMA = [
        'id' => 'int',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'description' => 'string|null',
        'views' => 'int',
        'creationdatetime' => 'int', // Unix timestamp
        'imagename' => 'string',
        'imagesize' => 'int|null',
        'imagemimetype' => 'string|null',
        'backupname' => 'string|null',
        'backupsize' => 'int|null',
        'backupmimetype' => 'string|null',
        'updatedat' => 'int|null', // Unix timestamp or null
        'location' => 'string|null',
        'exifexposure' => 'string|null',
        'exifaperture' => 'string|null',
        'exifiso' => 'int|null',
        'exiffocallength' => 'float|null',
        'exifcamera' => 'string|null',
        'exifcreationdate' => 'int|null', // Unix timestamp or null
    ];

    // =========================================================================
    // Track Schemas
    // =========================================================================

    /**
     * Schema for Track list endpoint: GET /api/track (api-public group)
     */
    public const TRACK_PUBLIC_SCHEMA = [
        'id' => 'int',
        'creationdatetime' => 'int', // Unix timestamp
        'startdatetime' => 'int|null', // Unix timestamp
        'enddatetime' => 'int|null', // Unix timestamp
        'distance' => 'float|null',
        'points' => 'int|null',
        'startpoint' => 'int|null',
        'endpoint' => 'int|null',
        'polyline' => 'string|null',
        'reducedpolyline' => 'string|null',
    ];

    /**
     * Schema for Track detail with user info (api-private group)
     */
    public const TRACK_PRIVATE_SCHEMA = [
        'id' => 'int',
        'username' => 'string|null',
        'creationdatetime' => 'int',
        'startdatetime' => 'int|null',
        'enddatetime' => 'int|null',
        'distance' => 'float|null',
        'points' => 'int|null',
        'startpoint' => 'int|null',
        'endpoint' => 'int|null',
        'polyline' => 'string|null',
        'reducedpolyline' => 'string|null',
        'user?' => 'array', // User object
    ];

    /**
     * Schema for Track in timelapse context
     */
    public const TRACK_TIMELAPSE_SCHEMA = [
        'id' => 'int',
        'username' => 'string|null',
        'user?' => 'array',
        'creationdatetime' => 'int',
        'startdatetime' => 'int|null',
        'enddatetime' => 'int|null',
        'distance' => 'float|null',
        'points' => 'int|null',
        'startpoint' => 'int|null',
        'endpoint' => 'int|null',
        'polyline' => 'string|null',
        'reducedpolyline' => 'string|null',
        'colorred' => 'int',
        'colorgreen' => 'int',
        'colorblue' => 'int',
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
     */
    public const CYCLE_SCHEMA = [
        'id' => 'int',
        'city' => 'array', // City object
        'dayofweek' => 'int',
        'weekofmonth' => 'int|null',
        'time' => 'int|null', // Unix timestamp
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'createdat' => 'int', // Unix timestamp
        'updatedat' => 'int|null',
        'disabledat' => 'int|null',
        'validfrom' => 'int|null', // Unix timestamp (date)
        'validuntil' => 'int|null', // Unix timestamp (date)
        'ridecalculatorfqcn?' => 'string|null',
        'description?' => 'string|null',
        'specialdayofweek?' => 'string|null',
        'specialweekofmonth?' => 'string|null',
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
        'datetime' => 'int|null', // Unix timestamp
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'createdat' => 'int', // Unix timestamp
        'updatedat' => 'int|null',
    ];

    /**
     * Schema for Subride detail with ride info (extended-subride-list group)
     */
    public const SUBRIDE_EXTENDED_SCHEMA = [
        'id' => 'int',
        'title' => 'string',
        'description' => 'string|null',
        'timestamp' => 'int',
        'datetime' => 'int|null',
        'location' => 'string|null',
        'latitude' => 'float|null',
        'longitude' => 'float|null',
        'createdat' => 'int',
        'updatedat' => 'int|null',
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
        'city_id' => 'int', // Relation type serialized name
        'createdat' => 'int|null',
        'lastfetchsuccessdatetime' => 'int|null',
        'lastfetchfailuredatetime' => 'int|null',
        'lastfetchfailureerror' => 'string|null',
        'autofetch' => 'bool',
        'autopublish' => 'bool',
        'additionaldata' => 'array|null',
    ];

    // =========================================================================
    // SocialNetworkFeedItem Schemas
    // =========================================================================

    /**
     * Schema for SocialNetworkFeedItem
     */
    public const SOCIAL_NETWORK_FEED_ITEM_SCHEMA = [
        'id' => 'int',
        'social_network_profile_id' => 'int', // Relation type
        'uniqueidentifier' => 'string',
        'permalink' => 'string|null',
        'title' => 'string|null',
        'text' => 'string',
        'datetime' => 'int', // Unix timestamp
        'hidden' => 'bool',
        'deleted' => 'bool',
        'createdat' => 'int', // Unix timestamp
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
        'weatherdatetime' => 'int', // Unix timestamp
        'creationdatetime' => 'int', // Unix timestamp
        'temperaturemin' => 'float',
        'temperaturemax' => 'float',
        'temperaturemorning' => 'float',
        'temperatureday' => 'float',
        'temperatureevening' => 'float',
        'temperaturenight' => 'float',
        'pressure' => 'float',
        'humidity' => 'float',
        'weathercode' => 'int',
        'weather' => 'string',
        'weatherdescription' => 'string',
        'weathericon' => 'string',
        'windspeed' => 'float',
        'winddirection' => 'float',
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
