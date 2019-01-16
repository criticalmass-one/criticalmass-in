<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Repository\CityCycleRepository;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RideGeneratorTest extends KernelTestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    protected function getCityRepository(): CityRepository
    {
        return $this->entityManager->getRepository(City::class);
    }

    protected function getCityCycleRepository(): CityCycleRepository
    {
        return $this->entityManager->getRepository(CityCycle::class);
    }

    public function tetsFoo(): void
    {
        /** @var City $hamburg */
        $hamburg = $this->getCityRepository()->find(1);

        $this->assertEquals('Hamburg', $hamburg->getCity());
    }

    public function testHamburg(): void
    {
        $hamburg = $this->getCityRepository()->findOneByCity('Hamburg');

        $from = new \DateTime('2018-12-01');
        $until = new \DateTime('2018-12-31');

        $hamburgCycles = $this->getCityCycleRepository()->findByCity($hamburg, $from, $until);

        $this->assertEquals(1, count($hamburgCycles));
    }

    public function testBerlin(): void
    {
        $berlin = $this->getCityRepository()->findOneByCity('Berlin');

        $from = new \DateTime('2018-12-01');
        $until = new \DateTime('2018-12-31');

        $berlinCycles = $this->getCityCycleRepository()->findByCity($berlin, $from, $until);

        $this->assertEquals(2, count($berlinCycles));
    }

    public function testHalle(): void
    {
        $halle = $this->getCityRepository()->findOneByCity('Halle');

        $from = new \DateTime('2018-12-01');
        $until = new \DateTime('2018-12-31');

        $halleCycles = $this->getCityCycleRepository()->findByCity($halle, $from, $until);

        $this->assertEquals(1, count($halleCycles));
    }

    public function testMainz1(): void
    {
        $mainz = $this->getCityRepository()->findOneByCity('Mainz');

        $from = new \DateTime('2018-09-01');
        $until = new \DateTime('2018-09-30');

        $mainzCycles = $this->getCityCycleRepository()->findByCity($mainz, $from, $until);

        $this->assertEquals(1, count($mainzCycles));
    }

    public function testMainz2(): void
    {
        $mainz = $this->getCityRepository()->findOneByCity('Mainz');

        $from = new \DateTime('2018-10-01');
        $until = new \DateTime('2018-10-31');

        $mainzCycles = $this->getCityCycleRepository()->findByCity($mainz, $from, $until);

        $this->assertEquals(1, count($mainzCycles));
    }

    public function testEsslingen(): void
    {
        $esslingen = $this->getCityRepository()->findOneByCity('Esslingen');

        $from = new \DateTime('2018-10-01');
        $until = new \DateTime('2018-10-31');

        $esslingenCycles = $this->getCityCycleRepository()->findByCity($esslingen, $from, $until);

        $this->assertEquals(0, count($esslingenCycles));
    }
}
