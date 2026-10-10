<?php declare(strict_types=1);

namespace Tests\Controller\SocialNetwork;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tests\Controller\AbstractControllerTestCase;

class SocialNetworkManagementControllerTest extends AbstractControllerTestCase
{
    private const string FORM_NAME = 'social_network_profile_edit';

    /**
     * Ohne Feeds-API waere die Netzwerkauswahl leer und jedes Formular ungueltig.
     */
    private function createClientWithNetworks(): KernelBrowser
    {
        $client = static::createClient();
        $client->disableReboot();

        static::getContainer()->set(NetworkManagerInterface::class, new class implements NetworkManagerInterface {
            /** @return array<string, NetworkInterface> */
            public function getNetworkList(): array
            {
                return [
                    'twitter' => new Network(1, 'twitter', 'Twitter', 'fab fa-twitter', '#1da1f2', '#ffffff', ''),
                    'homepage' => new Network(2, 'homepage', 'Homepage', 'far fa-globe', '#ffffff', '#000000', ''),
                ];
            }

            public function hasNetwork(string $identifier): bool
            {
                return array_key_exists($identifier, $this->getNetworkList());
            }

            public function getNetwork(string $identifier): NetworkInterface
            {
                return $this->getNetworkList()[$identifier];
            }
        });

        return $client;
    }

    private function getHamburgTwitterProfile(): SocialNetworkProfile
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        $profile = $em->getRepository(SocialNetworkProfile::class)
            ->findOneBy(['identifier' => 'https://twitter.com/criticalmassHH']);
        $this->assertNotNull($profile, 'Hamburg twitter profile fixture should exist');

        return $profile;
    }

    public function testInvalidEditShowsFormAgain(): void
    {
        $client = $this->createClientWithNetworks();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $profile = $this->getHamburgTwitterProfile();

        $crawler = $client->request('GET', sprintf('/socialnetwork/%d/edit', $profile->getId()));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Speichern')->form();
        $form[self::FORM_NAME . '[identifier]'] = '';

        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Soziales Netzwerk editieren', $client->getResponse()->getContent());

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();
        $this->assertSame(
            'https://twitter.com/criticalmassHH',
            $em->getRepository(SocialNetworkProfile::class)->find($profile->getId())->getIdentifier()
        );
    }

    public function testEditCityProfileRedirectsToCityList(): void
    {
        $client = $this->createClientWithNetworks();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $profile = $this->getHamburgTwitterProfile();
        $profileId = $profile->getId();

        $crawler = $client->request('GET', sprintf('/socialnetwork/%d/edit', $profileId));
        $form = $crawler->selectButton('Speichern')->form();

        // Unveraendert absenden: der Test prueft nur das Weiterleitungsziel.
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertStringEndsWith('/hamburg/socialnetwork/list', (string) $client->getResponse()->headers->get('Location'));
    }

    public function testEditRideProfileRedirectsToRideList(): void
    {
        $client = $this->createClientWithNetworks();
        $this->loginAs($client, 'testuser@criticalmass.in');

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

        $profile = (new SocialNetworkProfile())
            ->setRide($ride)
            ->setCreatedBy($em->getRepository(User::class)->findOneBy(['email' => 'testuser@criticalmass.in']))
            ->setNetwork('homepage')
            ->setIdentifier('https://example.org/tour-1571')
            ->setEnabled(true)
            ->setCreatedAt(new \DateTime());
        $em->persist($profile);
        $em->flush();
        $profileId = $profile->getId();
        $expectedListPath = sprintf('/hamburg/%s/socialnetwork/list', $ride->getDateTime()->format('Y-m-d'));

        try {
            $crawler = $client->request('GET', sprintf('/socialnetwork/%d/edit', $profileId));
            $form = $crawler->selectButton('Speichern')->form();
            $form[self::FORM_NAME . '[identifier]'] = 'https://example.org/tour-1571-neu';

            $client->submit($form);

            $this->assertTrue($client->getResponse()->isRedirect(), sprintf(
                'Expected a redirect, got %d',
                $client->getResponse()->getStatusCode()
            ));
            $this->assertStringEndsWith($expectedListPath, (string) $client->getResponse()->headers->get('Location'));
        } finally {
            $em = static::getContainer()->get('doctrine')->getManager();
            $em->clear();
            $em->remove($em->getRepository(SocialNetworkProfile::class)->find($profileId));
            $em->flush();
        }
    }
}
