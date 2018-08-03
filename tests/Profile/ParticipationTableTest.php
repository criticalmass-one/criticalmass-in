<?php declare(strict_types=1);

namespace Tests\Profile;

use App\Criticalmass\Profile\ParticipationTable\TableGenerator;
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

    public function test1(): void
    {
        $table = $this->getTableGenerator()->getTable();

        $this->assertEquals(0, count($table));
    }
}
