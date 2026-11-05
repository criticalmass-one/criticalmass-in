<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\User;

class SubrideAddTimeTest extends AbstractControllerTestCase
{
    public function testTheFormSuggestsTheLocalTimeOfTheRide(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $city = $entityManager->getRepository(City::class)->findOneBy([]);
        $this->assertNotNull($city);
        $originalTimezone = $city->getTimezone();
        $city->setTimezone('Europe/Berlin');

        $ride = (new Ride())
            ->setCity($city)
            ->setTitle('Tour mit Mini-Mass')
            ->setDateTime(new \DateTime('1991-07-26 17:00:00', new \DateTimeZone('UTC')))
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setEnabled(true);
        $entityManager->persist($ride);
        $entityManager->flush();

        try {
            $user = $entityManager->getRepository(User::class)->findOneBy([]);
            $this->loginAs($client, $user->getEmail());

            $crawler = $client->request('GET', sprintf('/%s/1991-07-26/addsubride', $city->getMainSlugString()));

            $this->assertResponseIsSuccessful();
            $this->assertSame('19:00', $crawler->filter('[name="subride[time]"]')->attr('value'));
        } finally {
            $entityManager->remove($entityManager->find(Ride::class, $ride->getId()));
            $entityManager->find(City::class, $city->getId())->setTimezone($originalTimezone ?? 'Europe/Berlin');
            $entityManager->flush();
        }
    }
}
