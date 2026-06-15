<?php declare(strict_types=1);

namespace Tests\Mcp;

use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\Weather;
use App\Repository\ParticipationRepository;

/**
 * Integrationstests der schreibenden Tools: führen das Tool über /mcp aus und
 * prüfen den persistierten Zustand.
 */
final class WriteToolsTest extends AbstractMcpTestCase
{
    public function testCreateCityPersistsCityAndSlug(): void
    {
        $token = $this->obtainAccessToken('city:write');

        $result = $this->callTool($token, 'create_city', [
            'citySlug' => 'bremen',
            'city' => ['city' => 'Bremen'],
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $slug = $this->em()->getRepository(CitySlug::class)->findOneBy(['slug' => 'bremen']);
        self::assertNotNull($slug);
        self::assertSame('Bremen', $slug->getCity()?->getCity());
    }

    public function testCreateCityRejectsDuplicateSlug(): void
    {
        $this->createCity('Hamburg', 'hamburg');
        $token = $this->obtainAccessToken('city:write');

        $result = $this->callTool($token, 'create_city', [
            'citySlug' => 'hamburg',
            'city' => ['city' => 'Hamburg'],
        ]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('existiert bereits', $result['text']);
    }

    public function testCreateRidePersistsRide(): void
    {
        $this->createCity('Hamburg', 'hamburg');
        $token = $this->obtainAccessToken('ride:write');

        $result = $this->callTool($token, 'create_ride', [
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2026-10-10',
            'ride' => ['title' => 'Oktober-Tour'],
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $rides = $this->em()->getRepository(Ride::class)->findBy(['title' => 'Oktober-Tour']);
        self::assertCount(1, $rides);
    }

    public function testUpdateRideChangesTitle(): void
    {
        $city = $this->createCity('Hamburg', 'hamburg');
        $ride = $this->createRide($city, '2026-09-01 19:00:00', 'Alt');
        $token = $this->obtainAccessToken('ride:write');

        $result = $this->callTool($token, 'update_ride', [
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2026-09-01',
            'ride' => ['title' => 'Neu'],
        ]);

        self::assertFalse($result['isError'], $result['text']);

        // Frisch aus der DB lesen (gecachte Test-Instanz umgehen).
        $rideId = $ride->getId();
        $this->em()->clear();
        $updated = $this->em()->getRepository(Ride::class)->find($rideId);
        self::assertSame('Neu', $updated?->getTitle());
    }

    public function testSetParticipationPersistsForTokenUser(): void
    {
        $city = $this->createCity('Hamburg', 'hamburg');
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('participation:write');

        $result = $this->callTool($token, 'set_participation', [
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2026-09-01',
            'status' => 'yes',
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $participation = $this->client->getContainer()->get(ParticipationRepository::class)
            ->findParticipationForUserAndRide($this->user, $ride);
        self::assertNotNull($participation);
    }

    public function testSetParticipationRejectsInvalidStatus(): void
    {
        $city = $this->createCity('Hamburg', 'hamburg');
        $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('participation:write');

        $result = $this->callTool($token, 'set_participation', [
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2026-09-01',
            'status' => 'vielleicht-nicht',
        ]);

        self::assertTrue($result['isError']);
    }

    public function testCreateRideEstimatePersists(): void
    {
        $city = $this->createCity('Hamburg', 'hamburg');
        $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('estimate:write');

        $result = $this->callTool($token, 'create_ride_estimate', [
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2026-09-01',
            'estimation' => 250,
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(RideEstimate::class)->findAll());
    }

    public function testSetWeatherPersists(): void
    {
        $city = $this->createCity('Hamburg', 'hamburg');
        $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('weather:write');

        $result = $this->callTool($token, 'set_weather', [
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2026-09-01',
            'weather' => ['temperatureMax' => 21.5, 'temperatureMin' => 12.0],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(Weather::class)->findAll());
    }

    public function testCreateCyclePersists(): void
    {
        $this->createCity('Hamburg', 'hamburg');
        $token = $this->obtainAccessToken('cycle:write');

        $result = $this->callTool($token, 'create_cycle', [
            'citySlug' => 'hamburg',
            'cycle' => ['dayOfWeek' => 5, 'weekOfMonth' => 0],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(\App\Entity\CityCycle::class)->findAll());
    }

    public function testCreateSocialProfilePersists(): void
    {
        $this->createCity('Hamburg', 'hamburg');
        $token = $this->obtainAccessToken('socialnetwork:write');

        $result = $this->callTool($token, 'create_social_profile', [
            'citySlug' => 'hamburg',
            'profile' => ['network' => 'twitter', 'identifier' => 'cm_hamburg'],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(\App\Entity\SocialNetworkProfile::class)->findAll());
    }

    public function testCreateCityActivityPersistsAndUpdatesScore(): void
    {
        $city = $this->createCity('Hamburg', 'hamburg');
        $token = $this->obtainAccessToken('activity:write');

        $result = $this->callTool($token, 'create_city_activity', [
            'citySlug' => 'hamburg',
            'score' => 0.42,
            'details' => [
                ['signalType' => 'participation', 'normalizedScore' => 0.5, 'rawCount' => 10],
                ['signalType' => 'photo', 'normalizedScore' => 0.3, 'rawCount' => 4],
                ['signalType' => 'track', 'normalizedScore' => 0.2, 'rawCount' => 2],
                ['signalType' => 'social_feed', 'normalizedScore' => 0.7, 'rawCount' => 20],
            ],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(\App\Entity\CityActivity::class)->findAll());

        $cityId = $city->getId();
        $this->em()->clear();
        $reloaded = $this->em()->getRepository(\App\Entity\City::class)->find($cityId);
        self::assertSame(0.42, $reloaded?->getActivityScore());
    }

    public function testCreateCityActivityRejectsMissingSignal(): void
    {
        $this->createCity('Hamburg', 'hamburg');
        $token = $this->obtainAccessToken('activity:write');

        $result = $this->callTool($token, 'create_city_activity', [
            'citySlug' => 'hamburg',
            'score' => 0.42,
            'details' => [
                ['signalType' => 'participation', 'normalizedScore' => 0.5, 'rawCount' => 10],
            ],
        ]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('Signaltyp', $result['text']);
    }

    public function testWriteToolRejectedWithoutScope(): void
    {
        $this->createCity('Hamburg', 'hamburg');
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'create_city', [
            'citySlug' => 'bremen',
            'city' => ['city' => 'Bremen'],
        ]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('Fehlender Scope', $result['text']);
    }
}
