<?php declare(strict_types=1);

namespace Tests\Controller\SocialNetwork;

use App\Entity\Ride;
use Tests\Controller\AbstractControllerTestCase;

class SocialNetworkListControllerTest extends AbstractControllerTestCase
{
    private function buildRideListUrl(): string
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $ride = $em->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :citySlug')
            ->setParameter('citySlug', 'hamburg')
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        return sprintf(
            '/%s/%s/socialnetwork/list',
            $ride->getCity()->getMainSlugString(),
            $ride->getDateTime()->format('Y-m-d')
        );
    }

    public function testRideListRedirectsGuestToLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', $this->buildRideListUrl());

        $this->assertTrue($client->getResponse()->isRedirect(), sprintf(
            'Expected a redirect to the login, got %d',
            $client->getResponse()->getStatusCode()
        ));
        $this->assertStringContainsString('/login', (string) $client->getResponse()->headers->get('Location'));
    }

    public function testCityListRedirectsGuestToLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/socialnetwork/list');

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertStringContainsString('/login', (string) $client->getResponse()->headers->get('Location'));
    }

    public function testRideListRendersForUser(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', $this->buildRideListUrl());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Soziale Netzwerke', $client->getResponse()->getContent());
    }
}
