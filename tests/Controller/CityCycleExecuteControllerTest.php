<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\CityCycle;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CityCycleExecuteControllerTest extends AbstractControllerTestCase
{
    /**
     * Beide Layouts geben Flash-Meldungen escaped aus. HTML im Text erschiene
     * dort als sichtbare Tags.
     */
    public function testPersistFlashContainsNoMarkup(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        // Der Tourengenerator liefert keine Touren, so legt der Test nichts an.
        static::getContainer()->set('http_client', new MockHttpClient(fn (): MockResponse => new MockResponse('[]')));

        $this->loginAs($client, 'testuser@criticalmass.in');

        $cycle = static::getContainer()->get('doctrine')->getRepository(CityCycle::class)
            ->createQueryBuilder('cc')
            ->join('cc.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $crawler = $client->request('GET', sprintf('/hamburg/cycles/%d/execute', $cycle->getId()));
        $this->assertResponseIsSuccessful();

        $crawler = $client->submit($crawler->selectButton('Weiter zur Vorschau')->form());
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[action*="execute-persist"]')->form());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();

        $alert = $crawler->filter('.alert-success');
        self::assertCount(1, $alert);
        self::assertSame('Es wurden 0 Touren automatisch angelegt.', trim($alert->text()));
    }
}
