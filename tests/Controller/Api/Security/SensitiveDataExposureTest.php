<?php declare(strict_types=1);

namespace Tests\Controller\Api\Security;

use App\Entity\Ride;
use App\Entity\Track;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Security test to ensure no sensitive data is exposed via API responses.
 *
 * This test checks all API endpoints for fields that should never be exposed
 * publicly, such as passwords, email addresses, tokens, and other PII.
 */
class SensitiveDataExposureTest extends AbstractApiControllerTestCase
{
    /**
     * List of field names that should NEVER appear in API responses.
     * These are considered sensitive and their exposure would be a security issue.
     */
    private const FORBIDDEN_FIELDS = [
        // Authentication & Security
        'password',
        'plainPassword',
        'plain_password',
        'passwordHash',
        'password_hash',
        'hashedPassword',
        'hashed_password',
        'salt',
        'token',
        'apiToken',
        'api_token',
        'apiKey',
        'api_key',
        'secret',
        'secretKey',
        'secret_key',
        'confirmationToken',
        'confirmation_token',
        'resetToken',
        'reset_token',
        'passwordResetToken',
        'password_reset_token',

        // Personal Identifiable Information (PII)
        'email',
        'emailAddress',
        'email_address',
        'emailCanonical',
        'email_canonical',
        'phone',
        'phoneNumber',
        'phone_number',
        'mobilePhone',
        'mobile_phone',
        'address',
        'streetAddress',
        'street_address',
        'homeAddress',
        'home_address',
        'zipCode',
        'zip_code',
        'postalCode',
        'postal_code',
        'socialSecurityNumber',
        'social_security_number',
        'ssn',
        'dateOfBirth',
        'date_of_birth',
        'birthDate',
        'birth_date',

        // Session & Internal Data
        'sessionId',
        'session_id',
        'cookieToken',
        'cookie_token',
        'roles',  // User roles should not be exposed publicly
        'internalId',
        'internal_id',

        // IP & Device Information
        'ipAddress',
        'ip_address',
        'lastLoginIp',
        'last_login_ip',
        'registrationIp',
        'registration_ip',
        'deviceId',
        'device_id',
        'userAgent',
        'user_agent',
    ];

    /**
     * Fields that are acceptable when they appear as part of a larger context
     * but might match partial patterns. These are NOT forbidden.
     */
    private const ALLOWED_CONTEXT_FIELDS = [
        'network',           // Social network type (twitter, facebook, etc.)
        'social_network',    // Social network reference
        'timezone',          // Timezone is public info
    ];

    #[TestDox('GET /api/city should not expose sensitive data')]
    public function testCityListDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/city');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/{citySlug} should not expose sensitive data')]
    public function testCityShowDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/ride should not expose sensitive data')]
    public function testRideListDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/ride');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier} should not expose sensitive data')]
    public function testRideShowDoesNotExposeSensitiveData(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();

        if (empty($rides)) {
            $this->markTestSkipped('No rides in database');
        }

        /** @var Ride $ride */
        $ride = $rides[0];
        $citySlug = $ride->getCity()->getMainSlugString();
        $dateString = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/%s/%s', $citySlug, $dateString));
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/{citySlug}/current should not expose sensitive data')]
    public function testCurrentRideDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/hamburg/current');

        if ($this->client->getResponse()->getStatusCode() === 200) {
            $this->assertNoSensitiveDataInResponse();
        } else {
            // No current ride is acceptable
            $this->assertTrue(true);
        }
    }

    #[TestDox('GET /api/photo should not expose sensitive data')]
    public function testPhotoListDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/photo');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/track should not expose sensitive data')]
    public function testTrackListDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/track');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/post should not expose sensitive data')]
    public function testPostListDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/post');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/track/{trackId} should not expose sensitive data')]
    public function testTrackShowDoesNotExposeSensitiveData(): void
    {
        $tracks = $this->entityManager->getRepository(Track::class)->findAll();

        if (empty($tracks)) {
            $this->markTestSkipped('No tracks in database');
        }

        /** @var Track $track */
        $track = $tracks[0];

        $this->client->request('GET', sprintf('/api/track/%d', $track->getId()));
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/cycles should not expose sensitive data')]
    public function testCyclesListDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/{citySlug}/location should not expose sensitive data')]
    public function testLocationListDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/hamburg/location');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-profiles should not expose sensitive data')]
    public function testSocialNetworkProfilesDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/socialnetwork-profiles should not expose sensitive data')]
    public function testGlobalSocialNetworkProfilesDoesNotExposeSensitiveData(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();
        $this->assertNoSensitiveDataInResponse();
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier}/listPhotos should not expose sensitive data')]
    public function testRidePhotosDoesNotExposeSensitiveData(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();

        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getMainSlugString();
            $dateString = $ride->getDateTime()->format('Y-m-d');

            $this->client->request('GET', sprintf('/api/%s/%s/listPhotos', $citySlug, $dateString));

            if ($this->client->getResponse()->getStatusCode() === 200) {
                $this->assertNoSensitiveDataInResponse();
                return;
            }
        }

        $this->markTestSkipped('No rides with photos found');
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier}/listTracks should not expose sensitive data')]
    public function testRideTracksDoesNotExposeSensitiveData(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();

        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getMainSlugString();
            $dateString = $ride->getDateTime()->format('Y-m-d');

            $this->client->request('GET', sprintf('/api/%s/%s/listTracks', $citySlug, $dateString));

            if ($this->client->getResponse()->getStatusCode() === 200) {
                $this->assertNoSensitiveDataInResponse();
                return;
            }
        }

        $this->markTestSkipped('No rides with tracks found');
    }

    #[DataProvider('allApiEndpointsProvider')]
    #[TestDox('API endpoint $endpoint should not expose sensitive data')]
    public function testApiEndpointDoesNotExposeSensitiveData(string $endpoint): void
    {
        $this->client->request('GET', $endpoint);

        // Only check successful responses
        if ($this->client->getResponse()->getStatusCode() === 200) {
            $this->assertNoSensitiveDataInResponse();
        } else {
            // Non-200 responses are acceptable (404, etc.)
            $this->assertTrue(true);
        }
    }

    public static function allApiEndpointsProvider(): array
    {
        return [
            'city list' => ['/api/city'],
            'city hamburg' => ['/api/hamburg'],
            'city berlin' => ['/api/berlin'],
            'city munich' => ['/api/munich'],
            'ride list' => ['/api/ride'],
            'photo list' => ['/api/photo'],
            'track list' => ['/api/track'],
            'post list' => ['/api/post'],
            'cycles list' => ['/api/cycles'],
            'hamburg current' => ['/api/hamburg/current'],
            'berlin current' => ['/api/berlin/current'],
            'hamburg locations' => ['/api/hamburg/location'],
            'berlin locations' => ['/api/berlin/location'],
            'hamburg social profiles' => ['/api/hamburg/socialnetwork-profiles'],
            'global social profiles' => ['/api/socialnetwork-profiles'],
        ];
    }

    /**
     * Recursively checks the JSON response for any forbidden field names.
     */
    private function assertNoSensitiveDataInResponse(): void
    {
        $content = $this->client->getResponse()->getContent();

        if (empty($content)) {
            return;
        }

        $data = json_decode($content, true);

        if ($data === null) {
            return;
        }

        $foundFields = $this->findForbiddenFields($data);

        if (!empty($foundFields)) {
            $this->fail(sprintf(
                "Sensitive data exposure detected! The following forbidden fields were found in the API response:\n\n%s\n\nEndpoint: %s",
                implode("\n", array_map(fn($f) => "  - {$f['path']}: '{$f['field']}'", $foundFields)),
                $this->client->getRequest()->getUri()
            ));
        }

        $this->assertTrue(true, 'No sensitive data found in response');
    }

    /**
     * Recursively searches for forbidden field names in the data structure.
     *
     * @param mixed $data The data to search
     * @param string $path The current path in the data structure
     * @return array List of found forbidden fields with their paths
     */
    private function findForbiddenFields(mixed $data, string $path = ''): array
    {
        $found = [];

        if (!is_array($data)) {
            return $found;
        }

        foreach ($data as $key => $value) {
            $currentPath = $path ? "{$path}.{$key}" : (string)$key;

            // Check if this key is a forbidden field
            if (is_string($key) && $this->isForbiddenField($key)) {
                $found[] = ['field' => $key, 'path' => $currentPath];
            }

            // Recursively check nested arrays/objects
            if (is_array($value)) {
                $found = array_merge($found, $this->findForbiddenFields($value, $currentPath));
            }
        }

        return $found;
    }

    /**
     * Checks if a field name is in the forbidden list.
     */
    private function isForbiddenField(string $fieldName): bool
    {
        $normalizedField = strtolower($fieldName);

        foreach (self::FORBIDDEN_FIELDS as $forbidden) {
            if (strtolower($forbidden) === $normalizedField) {
                return true;
            }
        }

        return false;
    }
}
