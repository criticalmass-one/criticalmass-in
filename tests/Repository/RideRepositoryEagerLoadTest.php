<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Regression für #1399: findRidesInInterval muss die Stadt (für die Kalender-
 * liste: ride.city.city + object_path(ride.city)) eager laden — sonst N+1.
 * Eine eager geladene Assoziation ist kein (uninitialisierter) Doctrine-Proxy.
 */
final class RideRepositoryEagerLoadTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get('doctrine')->getManager();
        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->em->getConnection();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }
        parent::tearDown();
    }

    public function testFindRidesInIntervalEagerLoadsCity(): void
    {
        $city = new City();
        $city->setCity('Teststadt ' . substr(md5(uniqid('', true)), 0, 8));
        $city->setTitle('Critical Mass Test');
        $city->setCreatedAt(new \DateTime());
        $this->em->persist($city);

        $slug = new CitySlug();
        $slug->setSlug('eager-' . substr(md5(uniqid('', true)), 0, 10));
        $slug->setCity($city);
        $this->em->persist($slug);
        $city->setMainSlug($slug);

        $ride = new Ride();
        $ride->setCity($city);
        $ride->setTitle('Eager-Test-Ride');
        $ride->setDateTime((new \DateTime())->add(new \DateInterval('P2D')));
        $this->em->persist($ride);
        $this->em->flush();

        // Identity Map leeren, damit die Abfrage frisch hydratisiert.
        $this->em->clear();

        /** @var RideRepository $repository */
        $repository = $this->em->getRepository(Ride::class);
        $rides = $repository->findRidesInInterval();

        $match = null;
        foreach ($rides as $candidate) {
            if ($candidate->getTitle() === 'Eager-Test-Ride') {
                $match = $candidate;
                break;
            }
        }

        self::assertNotNull($match, 'Der angelegte Ride muss im Intervall gefunden werden.');
        self::assertNotInstanceOf(
            Proxy::class,
            $match->getCity(),
            'Die Stadt muss eager geladen sein (kein Lazy-Proxy) — sonst N+1.',
        );
        self::assertSame($city->getCity(), $match->getCity()?->getCity());
    }
}
