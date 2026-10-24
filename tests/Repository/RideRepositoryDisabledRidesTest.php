<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Eine deaktivierte Tour (abgesagt, Stadt stillgelegt) darf auf der Stadtseite weder
 * als naechster Termin noch in der Tourliste erscheinen. Validator und Dublettensuche
 * muessen sie aber weiter sehen, sonst legt der Generator sie neu an.
 */
class RideRepositoryDisabledRidesTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?Ride $disabledRide = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    private function createDisabledRide(City $city): Ride
    {
        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setTitle('Abgesagte Testtour')
            ->setDateTime(new \DateTime('@' . (time() + 60)))
            ->setEnabled(false);

        $this->entityManager->persist($ride);
        $this->entityManager->flush();

        return $this->disabledRide = $ride;
    }

    private function getCity(): City
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);

        if (!$city) {
            $this->markTestSkipped('Hamburg fixture not found');
        }

        return $city;
    }

    private function getRepository(): RideRepository
    {
        return $this->entityManager->getRepository(Ride::class);
    }

    public function testCurrentRideSkipsDisabledRide(): void
    {
        $city = $this->getCity();
        $disabledRide = $this->createDisabledRide($city);

        $currentRide = $this->getRepository()->findCurrentRideForCity($city);

        $this->assertNotSame($disabledRide->getId(), $currentRide?->getId());
    }

    public function testRidesForCityHidesDisabledRideOnlyWhenAsked(): void
    {
        $city = $this->getCity();
        $disabledRide = $this->createDisabledRide($city);

        $allIds = array_map(fn (Ride $ride) => $ride->getId(), $this->getRepository()->findRidesForCity($city));
        $enabledIds = array_map(fn (Ride $ride) => $ride->getId(), $this->getRepository()->findRidesForCity($city, onlyEnabled: true));

        $this->assertContains($disabledRide->getId(), $allIds);
        $this->assertNotContains($disabledRide->getId(), $enabledIds);
    }

    protected function tearDown(): void
    {
        if ($this->disabledRide) {
            $this->entityManager->remove($this->disabledRide);
            $this->entityManager->flush();
        }

        parent::tearDown();
        $this->entityManager = null;
        $this->disabledRide = null;
    }
}
