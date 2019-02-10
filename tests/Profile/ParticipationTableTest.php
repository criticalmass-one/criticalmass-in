<?php declare(strict_types=1);

namespace Tests\Profile;

use App\Criticalmass\Profile\ParticipationTable\TableGeneratorInterface;
use App\Entity\Participation;
use App\Entity\Ride;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ParticipationTableTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function getTableGenerator(): TableGeneratorInterface
    {
        $container = self::$container;

        return $container->get(TableGeneratorInterface::class);
    }

    protected function createRide(\DateTime $dateTime): Ride
    {
        $ride = new Ride();
        $ride->setDateTime($dateTime);

        return $ride;
    }

    protected function createParticipation(\DateTime $dateTime): Participation
    {
        $participation = new Participation();
        $participation->setRide($this->createRide($dateTime));

        return $participation;
    }

    public function testEmptyTable(): void
    {
        $table = $this->getTableGenerator()->getTable();

        $this->assertEquals(0, count($table));
    }

    public function testOneParticipation(): void
    {
        $table = $this->getTableGenerator()->getTable();

        $table->addParticipation($this->createParticipation(new \DateTime('2018-01-01')));

        $this->assertEquals(1, count($table));
    }

    public function testTwoParticipations(): void
    {
        $table = $this->getTableGenerator()->getTable();

        $table
            ->addParticipation($this->createParticipation(new \DateTime('2018-01-01')))
            ->addParticipation($this->createParticipation(new \DateTime('2018-02-01')));

        $this->assertEquals(2, count($table));
    }

    public function testFourDifferentYearParticipations(): void
    {
        $table = $this->getTableGenerator()->getTable();

        $table
            ->addParticipation($this->createParticipation(new \DateTime('2015-01-01')))
            ->addParticipation($this->createParticipation(new \DateTime('2016-01-01')))
            ->addParticipation($this->createParticipation(new \DateTime('2017-01-01')))
            ->addParticipation($this->createParticipation(new \DateTime('2018-01-01')));

        $this->assertEquals(4, count($table));
    }

    public function testTwoRidesForADay(): void
    {
        $table = $this->getTableGenerator()->getTable();

        $table
            ->addParticipation($this->createParticipation(new \DateTime('2015-01-01 15:00:00')))
            ->addParticipation($this->createParticipation(new \DateTime('2015-01-01 19:00:00')));

        $this->assertEquals(2, count($table));
    }
}
