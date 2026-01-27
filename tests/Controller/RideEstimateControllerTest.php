<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Ride;

class RideEstimateControllerTest extends AbstractControllerTestCase
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

    public function testAnonymousEstimatePageAccessible(): void
    {
        $client = static::createClient();

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/anonymousestimate');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddEstimateRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('POST', $this->buildRideUrl($ride) . '/addestimate');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
