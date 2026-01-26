<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Ride;

class RideManagementControllerTest extends AbstractControllerTestCase
{
    private function getFirstRideForCity(string $citySlug): ?Ride
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :citySlug')
            ->setParameter('citySlug', $citySlug)
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function buildRideUrl(Ride $ride): string
    {
        return sprintf('/%s/%s', $ride->getCity()->getMainSlugString(), $ride->getDateTime()->format('Y-m-d'));
    }

    public function testRideEditAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRideEditRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRideEditFormContainsRideTitle(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $crawler = $client->request('GET', $this->buildRideUrl($ride) . '/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $titleField = $crawler->filter('input[name="ride[title]"]');
        $this->assertGreaterThan(0, $titleField->count(), 'Edit form should contain a title field');
        $this->assertEquals($ride->getTitle(), $titleField->attr('value'));
    }

    public function testRideDisableRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/disable');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRideEnableRedirectsWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/enable');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
