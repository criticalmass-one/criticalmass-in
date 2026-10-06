<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\City;
use App\Entity\Ride;

/**
 * Eine kommende Tour in einer eingeschlafenen Stadt warnt, dass sie
 * vielleicht nicht stattfindet.
 */
class InactiveCityRideHintTest extends AbstractControllerTestCase
{
    private const TITEL = 'Hinweisprobe';

    protected function tearDown(): void
    {
        if (static::$booted) {
            static::getContainer()->get('doctrine')->getManager()
                ->createQuery('DELETE FROM App\\Entity\\Ride r WHERE r.title = :titel')
                ->setParameter('titel', self::TITEL)
                ->execute();
        }

        parent::tearDown();
    }

    private function rideIn(string $cityName, string $when): string
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $city = $entityManager->getRepository(City::class)->findOneBy(['city' => $cityName]);

        $ride = new Ride();
        $ride->setCity($city);
        $ride->setTitle(self::TITEL);
        $ride->setDateTime(new \DateTime($when));
        $ride->setCreatedAt(new \DateTime());
        $ride->setUpdatedAt(new \DateTime());
        $ride->setEnabled(true);

        $entityManager->persist($ride);
        $entityManager->flush();

        return sprintf('/%s/%s', $city->getMainSlugString(), $ride->getDateTime()->format('Y-m-d'));
    }

    public function testUpcomingRideInInactiveCityWarns(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $this->rideIn('Ghosttown', '+3 years 19:00'));

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('#inactive-city-hint'));
    }

    public function testPastRideInInactiveCityDoesNotWarn(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $this->rideIn('Ghosttown', '-3 years 19:00'));

        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('#inactive-city-hint'));
    }
}
