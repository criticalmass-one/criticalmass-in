<?php declare(strict_types=1);

namespace Tests\OrderedEntities;

use App\Criticalmass\OrderedEntities\CriteriaBuilder\CriteriaBuilder;
use App\Criticalmass\OrderedEntities\OrderedEntityInterface;
use App\Criticalmass\OrderedEntities\SortOrder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class CriteriaBuilderTest extends TestCase
{
    public function testWithEmptyClass(): void
    {
        $criteriaBuilder = new CriteriaBuilder(new AnnotationReader());

        $actualCriteria = $criteriaBuilder->build((new Class() implements OrderedEntityInterface {}), SortOrder::ASC);

        $expectedCriteria = new Criteria();

        $this->assertEquals($expectedCriteria, $actualCriteria);
    }

    public function testWithTestClass(): void
    {
        $criteriaBuilder = new CriteriaBuilder(new AnnotationReader());

        $actualCriteria = $criteriaBuilder->build($this->createTestEntity(), SortOrder::ASC);

        $expectedCriteria = new Criteria();
        $expectedCriteria
            ->orderBy(['dateTime' => SortOrder::ASC])
            ->andWhere(Criteria::expr()->gt('dateTime', new \DateTime('2019-06-11 19:00:00')))
            ->andWhere(Criteria::expr()->eq('city', 'Hamburg'))
            ->andWhere(Criteria::expr()->eq('enabled', true))
            ->andWhere(Criteria::expr()->eq('deleted', false));

        $this->assertEquals($expectedCriteria, $actualCriteria);
    }

    protected function createTestEntity(): OrderedEntityInterface
    {
        $entity = new TestEntity();

        $entity
            ->setDateTime(new \DateTime('2019-06-11 19:00:00'))
            ->setCity('Hamburg')
            ->setEnabled(true)
            ->setDeleted(false);

        return $entity;
    }
}