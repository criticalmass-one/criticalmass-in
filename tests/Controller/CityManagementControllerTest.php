<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\City;

class CityManagementControllerTest extends AbstractControllerTestCase
{
    public function testCityEditAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/hamburg/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCityEditRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testCityEditFormContainsCityName(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/hamburg/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('input[name="city[city]"]');
        $cityField = $crawler->filter('input[name="city[city]"]');
        $this->assertEquals('Hamburg', $cityField->attr('value'));
    }

    public function testAddRideAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/hamburg/add-ride');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddRideRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/add-ride');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testEditCitySubmissionRedirects(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/hamburg/edit');

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['city[punchLine]'] = 'Testpunchline';

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testEditCityChangesLongDescriptionInDatabase(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/hamburg/edit');

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['city[longDescription]'] = 'Hamburg ist eine wunderschöne Hafenstadt an der Elbe.';

        $client->submit($form);

        $em = static::getContainer()->get('doctrine')->getManager();
        $city = $em->getRepository(City::class)
            ->createQueryBuilder('c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNotNull($city);
        $this->assertEquals('Hamburg ist eine wunderschöne Hafenstadt an der Elbe.', $city->getLongDescription());
    }

    public function testEditCityChangesDescriptionInDatabase(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/hamburg/edit');

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['city[description]'] = 'Die schönste Stadt im Norden.';

        $client->submit($form);

        $em = static::getContainer()->get('doctrine')->getManager();
        $city = $em->getRepository(City::class)
            ->createQueryBuilder('c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNotNull($city);
        $this->assertEquals('Die schönste Stadt im Norden.', $city->getDescription());
    }

    public function testEditCityChangesPunchLineInDatabase(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/hamburg/edit');

        $form = $crawler->filter('button[type="submit"]')->form();
        $form['city[punchLine]'] = 'Radfahren in Hamburg';

        $client->submit($form);

        $em = static::getContainer()->get('doctrine')->getManager();
        $city = $em->getRepository(City::class)
            ->createQueryBuilder('c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNotNull($city);
        $this->assertEquals('Radfahren in Hamburg', $city->getPunchLine());
    }
}
