<?php declare(strict_types=1);

namespace Tests\Mcp;

use App\Entity\CityActivity;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\SocialNetworkProfile;
use App\Entity\Weather;
use App\Repository\ParticipationRepository;

/**
 * Integrationstests der schreibenden Tools: führen das Tool über /mcp aus und
 * prüfen den persistierten Zustand. Assertions sind auf die im Test (in einer
 * zurückgerollten Transaktion) angelegten Entities gescopt, damit CI-Fixtures
 * nicht stören; Städte nutzen eindeutige Slugs.
 */
final class WriteToolsTest extends AbstractMcpTestCase
{
    private function uniqueSlug(): string
    {
        return 'mcp-new-' . substr(md5(uniqid('', true)), 0, 10);
    }

    public function testCreateCityPersistsCity(): void
    {
        // Die Slugs werden aus dem Namen abgeleitet (CitySlugHandler), daher über
        // einen eindeutigen Namen verifizieren.
        $name = 'Teststadt ' . substr(md5(uniqid('', true)), 0, 8);
        $token = $this->obtainAccessToken('city:write');

        $result = $this->callTool($token, 'create_city', [
            'citySlug' => $this->uniqueSlug(),
            'city' => ['city' => $name],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(\App\Entity\City::class)->findBy(['city' => $name]));
    }

    public function testCreateCityRejectsDuplicateSlug(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('city:write');

        $result = $this->callTool($token, 'create_city', [
            'citySlug' => $city->getMainSlugString(),
            'city' => ['city' => 'Hamburg'],
        ]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('existiert bereits', $result['text']);
    }

    public function testUpdateCityChangesTitle(): void
    {
        $city = $this->createCity('Alt-Stadt');
        $token = $this->obtainAccessToken('city:write');

        $result = $this->callTool($token, 'update_city', [
            'citySlug' => $city->getMainSlugString(),
            'city' => ['title' => 'Neuer Titel'],
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $cityId = $city->getId();
        $this->em()->clear();
        $updated = $this->em()->getRepository(\App\Entity\City::class)->find($cityId);
        self::assertSame('Neuer Titel', $updated?->getTitle());
    }

    public function testSetCityEnabledDisablesCity(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('city:write');

        $result = $this->callTool($token, 'set_city_enabled', [
            'citySlug' => $city->getMainSlugString(),
            'enabled' => false,
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertFalse($result['json']['enabled']);

        $cityId = $city->getId();
        $this->em()->clear();
        $reloaded = $this->em()->getRepository(\App\Entity\City::class)->find($cityId);
        self::assertFalse($reloaded?->isEnabled());
    }

    public function testSetCityEnabledRejectsNonBoolean(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('city:write');

        $result = $this->callTool($token, 'set_city_enabled', [
            'citySlug' => $city->getMainSlugString(),
            'enabled' => 'nein',
        ]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('Boolean', $result['text']);
    }

    public function testCreateRidePersistsRide(): void
    {
        $city = $this->createCity();
        $title = 'Oktober-Tour-' . uniqid();
        $token = $this->obtainAccessToken('ride:write');

        $result = $this->callTool($token, 'create_ride', [
            'citySlug' => $city->getMainSlugString(),
            'rideIdentifier' => '2026-10-10',
            'ride' => ['title' => $title],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(Ride::class)->findBy(['title' => $title]));
    }

    public function testUpdateRideChangesTitle(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00', 'Alt');
        $token = $this->obtainAccessToken('ride:write');

        $result = $this->callTool($token, 'update_ride', [
            'citySlug' => $city->getMainSlugString(),
            'rideIdentifier' => '2026-09-01',
            'ride' => ['title' => 'Neu'],
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $rideId = $ride->getId();
        $this->em()->clear();
        $updated = $this->em()->getRepository(Ride::class)->find($rideId);
        self::assertSame('Neu', $updated?->getTitle());
    }

    public function testSetRideEnabledDisablesRide(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('ride:write');

        $result = $this->callTool($token, 'set_ride_enabled', [
            'citySlug' => $city->getMainSlugString(),
            'rideIdentifier' => '2026-09-01',
            'enabled' => false,
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertFalse($result['json']['enabled']);

        $rideId = $ride->getId();
        $this->em()->clear();
        $reloaded = $this->em()->getRepository(Ride::class)->find($rideId);
        self::assertFalse($reloaded?->isEnabled());
    }

    public function testDeleteCycleKeepsRides(): void
    {
        $city = $this->createCity();
        $cycle = $this->createCityCycle($city);
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $ride->setCycle($cycle);
        $this->em()->flush();

        $cycleId = $cycle->getId();
        $rideId = $ride->getId();
        $token = $this->obtainAccessToken('cycle:write');

        $result = $this->callTool($token, 'delete_cycle', [
            'citySlug' => $city->getMainSlugString(),
            'cycleId' => $cycleId,
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $this->em()->clear();
        self::assertNull($this->em()->getRepository(CityCycle::class)->find($cycleId));
        $survivingRide = $this->em()->getRepository(Ride::class)->find($rideId);
        self::assertNotNull($survivingRide, 'Ride must survive cycle deletion');
        self::assertNull($survivingRide->getCycle());
    }

    public function testDeleteCycleRejectsForeignCity(): void
    {
        $city = $this->createCity();
        $otherCity = $this->createCity('Andere-Stadt');
        $cycle = $this->createCityCycle($otherCity);
        $token = $this->obtainAccessToken('cycle:write');

        $result = $this->callTool($token, 'delete_cycle', [
            'citySlug' => $city->getMainSlugString(),
            'cycleId' => $cycle->getId(),
        ]);

        self::assertTrue($result['isError']);
    }

    public function testSetParticipationPersistsForTokenUser(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('participation:write');

        $result = $this->callTool($token, 'set_participation', [
            'citySlug' => $city->getMainSlugString(),
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
        $city = $this->createCity();
        $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('participation:write');

        $result = $this->callTool($token, 'set_participation', [
            'citySlug' => $city->getMainSlugString(),
            'rideIdentifier' => '2026-09-01',
            'status' => 'vielleicht-nicht',
        ]);

        self::assertTrue($result['isError']);
    }

    public function testCreateRideEstimatePersists(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('estimate:write');

        $result = $this->callTool($token, 'create_ride_estimate', [
            'citySlug' => $city->getMainSlugString(),
            'rideIdentifier' => '2026-09-01',
            'estimation' => 250,
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(RideEstimate::class)->findBy(['ride' => $ride]));
    }

    public function testListRideEstimatesReturnsIds(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $estimate = $this->createRideEstimate($ride, 123);
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'list_ride_estimates', [
            'citySlug' => $city->getMainSlugString(),
            'rideIdentifier' => '2026-09-01',
        ]);

        self::assertFalse($result['isError'], $result['text']);
        $ids = array_column($result['json']['estimates'], 'id');
        self::assertContains($estimate->getId(), $ids);
    }

    public function testUpdateRideEstimateChangesEstimation(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $estimate = $this->createRideEstimate($ride, 100);
        $token = $this->obtainAccessToken('estimate:write');

        $result = $this->callTool($token, 'update_ride_estimate', [
            'estimateId' => $estimate->getId(),
            'estimation' => 555,
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $estimateId = $estimate->getId();
        $this->em()->clear();
        $updated = $this->em()->getRepository(RideEstimate::class)->find($estimateId);
        self::assertSame(555, $updated?->getEstimatedParticipants());
    }

    public function testDeleteRideEstimateRemovesIt(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $estimate = $this->createRideEstimate($ride, 100);
        $token = $this->obtainAccessToken('estimate:write');

        $estimateId = $estimate->getId();

        $result = $this->callTool($token, 'delete_ride_estimate', [
            'estimateId' => $estimateId,
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $this->em()->clear();
        self::assertNull($this->em()->getRepository(RideEstimate::class)->find($estimateId));
    }

    public function testUpdateRideEstimateRejectsUnknownId(): void
    {
        $token = $this->obtainAccessToken('estimate:write');

        $result = $this->callTool($token, 'update_ride_estimate', [
            'estimateId' => 999999999,
            'estimation' => 10,
        ]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('ID', $result['text']);
    }

    public function testSetWeatherPersists(): void
    {
        $city = $this->createCity();
        $ride = $this->createRide($city, '2026-09-01 19:00:00');
        $token = $this->obtainAccessToken('weather:write');

        $result = $this->callTool($token, 'set_weather', [
            'citySlug' => $city->getMainSlugString(),
            'rideIdentifier' => '2026-09-01',
            'weather' => ['temperatureMax' => 21.5, 'temperatureMin' => 12.0],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(Weather::class)->findBy(['ride' => $ride]));
    }

    public function testCreateCyclePersists(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('cycle:write');

        $result = $this->callTool($token, 'create_cycle', [
            'citySlug' => $city->getMainSlugString(),
            'cycle' => ['dayOfWeek' => 5, 'weekOfMonth' => 0],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(CityCycle::class)->findBy(['city' => $city]));
    }

    public function testCreateSocialProfilePersists(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('socialnetwork:write');

        $result = $this->callTool($token, 'create_social_profile', [
            'citySlug' => $city->getMainSlugString(),
            'profile' => ['network' => 'twitter', 'identifier' => 'cm_hamburg'],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(SocialNetworkProfile::class)->findBy(['city' => $city]));
    }

    public function testCreateLocationPersists(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('location:write');

        $result = $this->callTool($token, 'create_location', [
            'citySlug' => $city->getMainSlugString(),
            'location' => ['title' => 'Rathausmarkt', 'latitude' => 53.55, 'longitude' => 9.99],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(\App\Entity\Location::class)->findBy(['city' => $city]));
    }

    public function testUpdateLocationChangesTitle(): void
    {
        $city = $this->createCity();
        $location = $this->createLocation($city, 'treffpunkt');
        $token = $this->obtainAccessToken('location:write');

        $result = $this->callTool($token, 'update_location', [
            'citySlug' => $city->getMainSlugString(),
            'locationSlug' => 'treffpunkt',
            'location' => ['title' => 'Neuer Treffpunkt'],
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $locationId = $location->getId();
        $this->em()->clear();
        $updated = $this->em()->getRepository(\App\Entity\Location::class)->find($locationId);
        self::assertSame('Neuer Treffpunkt', $updated?->getTitle());
    }

    public function testDeleteLocationRemovesIt(): void
    {
        $city = $this->createCity();
        $location = $this->createLocation($city, 'treffpunkt');
        $locationId = $location->getId();
        $token = $this->obtainAccessToken('location:write');

        $result = $this->callTool($token, 'delete_location', [
            'citySlug' => $city->getMainSlugString(),
            'locationSlug' => 'treffpunkt',
        ]);

        self::assertFalse($result['isError'], $result['text']);

        $this->em()->clear();
        self::assertNull($this->em()->getRepository(\App\Entity\Location::class)->find($locationId));
    }

    public function testGetLocationReturnsIt(): void
    {
        $city = $this->createCity();
        $this->createLocation($city, 'treffpunkt');
        $token = $this->obtainAccessToken('city:read');

        $result = $this->callTool($token, 'get_location', [
            'citySlug' => $city->getMainSlugString(),
            'locationSlug' => 'treffpunkt',
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertSame('treffpunkt', $result['json']['slug']);
    }

    public function testCreateCityActivityPersistsAndUpdatesScore(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('activity:write');

        $result = $this->callTool($token, 'create_city_activity', [
            'citySlug' => $city->getMainSlugString(),
            'score' => 0.42,
            'details' => [
                ['signalType' => 'participation', 'normalizedScore' => 0.5, 'rawCount' => 10],
                ['signalType' => 'photo', 'normalizedScore' => 0.3, 'rawCount' => 4],
                ['signalType' => 'track', 'normalizedScore' => 0.2, 'rawCount' => 2],
                ['signalType' => 'social_feed', 'normalizedScore' => 0.7, 'rawCount' => 20],
            ],
        ]);

        self::assertFalse($result['isError'], $result['text']);
        self::assertCount(1, $this->em()->getRepository(CityActivity::class)->findBy(['city' => $city]));

        $cityId = $city->getId();
        $this->em()->clear();
        $reloaded = $this->em()->getRepository(\App\Entity\City::class)->find($cityId);
        self::assertSame(0.42, $reloaded?->getActivityScore());
    }

    public function testCreateCityActivityRejectsMissingSignal(): void
    {
        $city = $this->createCity();
        $token = $this->obtainAccessToken('activity:write');

        $result = $this->callTool($token, 'create_city_activity', [
            'citySlug' => $city->getMainSlugString(),
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
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->callTool($token, 'create_city', [
            'citySlug' => $this->uniqueSlug(),
            'city' => ['city' => 'Bremen'],
        ]);

        self::assertTrue($result['isError']);
        self::assertStringContainsString('Fehlender Scope', $result['text']);
    }
}
