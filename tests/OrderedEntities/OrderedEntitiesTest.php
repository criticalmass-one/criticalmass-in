<?php declare(strict_types=1);

namespace Tests\OrderedEntities;

use App\Criticalmass\OrderedEntities\OrderedEntitiesManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use PHPUnit\Framework\TestCase;

class OrderedEntitiesTest extends TestCase
{
    public function testFoo(): void
    {
        $entity = new TestEntity();

        $registry = $this->createMock(Registry::class);
        $manager = new OrderedEntitiesManager($registry);

        $previous = $manager->getPrevious($entity);
    }
}