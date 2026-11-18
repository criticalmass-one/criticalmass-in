<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\Weather;
use App\Repository\WeatherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Zu einer Tour liegen Vorhersagen fuer mehrere Tage. Angezeigt werden darf
 * nur die fuer den Tourtag — auch wenn danach noch eine fuer einen anderen
 * Tag angelegt wurde.
 */
class WeatherRepositoryTest extends KernelTestCase
{
    private const STADT = 'Wetterprobe';

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\\Entity\\Weather w WHERE w.ride IN (SELECT r FROM App\\Entity\\Ride r JOIN r.city c WHERE c.city = :stadt)')
            ->setParameter('stadt', self::STADT)
            ->execute();
        $this->entityManager->createQuery('DELETE FROM App\\Entity\\Ride r WHERE r.city IN (SELECT c FROM App\\Entity\\City c WHERE c.city = :stadt)')
            ->setParameter('stadt', self::STADT)
            ->execute();
        $this->entityManager->createQuery('DELETE FROM App\\Entity\\City c WHERE c.city = :stadt')
            ->setParameter('stadt', self::STADT)
            ->execute();

        parent::tearDown();
    }

    private function tour(string $zeitzone, string $utc): Ride
    {
        $stadt = new City();
        $stadt->setCity(self::STADT);
        $stadt->setTitle('Critical Mass ' . self::STADT);
        $stadt->setEnabled(true);
        $stadt->setLatitude(53.55);
        $stadt->setLongitude(9.99);
        $stadt->setTimezone($zeitzone);
        $this->entityManager->persist($stadt);

        $tour = new Ride();
        $tour->setCity($stadt);
        $tour->setTitle('Wetterprobe-Tour');
        $tour->setDateTime(new \DateTime($utc, new \DateTimeZone('UTC')));
        $tour->setCreatedAt(new \DateTime());
        $tour->setUpdatedAt(new \DateTime());
        $tour->setEnabled(true);
        $this->entityManager->persist($tour);

        return $tour;
    }

    private function wetter(Ride $tour, string $tag, string $angelegt, float $abends): Weather
    {
        $wetter = (new Weather())
            ->setRide($tour)
            ->setWeatherDateTime(new \DateTime($tag, new \DateTimeZone('UTC')))
            ->setCreationDateTime(new \DateTime($angelegt, new \DateTimeZone('UTC')))
            ->setTemperatureEvening($abends);
        $this->entityManager->persist($wetter);

        return $wetter;
    }

    private function repository(): WeatherRepository
    {
        $repository = $this->entityManager->getRepository(Weather::class);
        self::assertInstanceOf(WeatherRepository::class, $repository);

        return $repository;
    }

    public function testANewerForecastForAnotherDayDoesNotWin(): void
    {
        $tour = $this->tour('Europe/Berlin', '2031-03-14 18:00:00');
        $richtig = $this->wetter($tour, '2031-03-14', '2031-03-14 16:00:00', 13.5);
        $this->wetter($tour, '2031-03-19', '2031-03-14 20:00:00', 17.9);
        $this->entityManager->flush();

        self::assertSame($richtig->getId(), $this->repository()->findCurrentWeatherForRide($tour)?->getId());
    }

    public function testTheLatestForecastOfTheRideDayWins(): void
    {
        $tour = $this->tour('Europe/Berlin', '2031-03-14 18:00:00');
        $this->wetter($tour, '2031-03-14', '2031-03-12 16:00:00', 9.0);
        $neuer = $this->wetter($tour, '2031-03-14', '2031-03-14 16:00:00', 13.5);
        $this->entityManager->flush();

        self::assertSame($neuer->getId(), $this->repository()->findCurrentWeatherForRide($tour)?->getId());
    }

    /**
     * 14.03. 19:00 in San Francisco ist 15.03. 02:00 UTC — der Tourtag ist der 14.
     */
    public function testTheRideDayIsTheLocalDay(): void
    {
        $tour = $this->tour('America/Los_Angeles', '2031-03-15 02:00:00');
        $richtig = $this->wetter($tour, '2031-03-14', '2031-03-13 16:00:00', 15.0);
        $this->wetter($tour, '2031-03-15', '2031-03-14 16:00:00', 18.0);
        $this->entityManager->flush();

        self::assertSame($richtig->getId(), $this->repository()->findCurrentWeatherForRide($tour)?->getId());
    }

    public function testWithoutAForecastForTheRideDayThereIsNone(): void
    {
        $tour = $this->tour('Europe/Berlin', '2031-03-14 18:00:00');
        $this->wetter($tour, '2031-03-19', '2031-03-14 20:00:00', 17.9);
        $this->entityManager->flush();

        self::assertNull($this->repository()->findCurrentWeatherForRide($tour));
    }
}
