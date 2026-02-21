<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Post;
use App\Entity\Ride;

class PostControllerTest extends AbstractControllerTestCase
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

    public function testWriteRidePostFormAccessible(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', sprintf('/post/write/ride/%d', $ride->getId()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testWriteRidePostRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', sprintf('/post/write/ride/%d', $ride->getId()));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testSubmitRidePostRedirects(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $crawler = $client->request('GET', sprintf('/post/write/ride/%d', $ride->getId()));

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['post[message]'] = 'Tolle Tour, hat Spaß gemacht!';

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRidePostExistsInDatabase(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getFirstRideForCity('hamburg');
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');
        $rideId = $ride->getId();

        $crawler = $client->request('GET', sprintf('/post/write/ride/%d', $rideId));

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['post[message]'] = 'Einzigartiger Testkommentar 12345';

        $client->submit($form);

        $em = static::getContainer()->get('doctrine')->getManager();
        $post = $em->getRepository(Post::class)->findOneBy(['message' => 'Einzigartiger Testkommentar 12345']);

        $this->assertNotNull($post, 'Post should exist in database after submission');
        $this->assertNotNull($post->getRide(), 'Post should be associated with a ride');
    }
}
