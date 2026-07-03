<?php declare(strict_types=1);

namespace Tests\Controller;

/**
 * Regression coverage for #1366: opening the "add social network profile" page
 * for a city returned a 500. The city route has no {rideIdentifier}, but the
 * controller's nullable ?Ride argument still triggers RideValueResolver, which
 * called rtrim() on the missing (null) rideIdentifier and raised a TypeError.
 */
class SocialNetworkControllerTest extends AbstractControllerTestCase
{
    public function testCityAddPageLoadsForLoggedInUser(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/hamburg/socialnetwork/add');

        self::assertResponseIsSuccessful();
    }

    public function testCityAddPageRedirectsAnonymousToLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/socialnetwork/add');

        self::assertResponseRedirects();
    }
}
