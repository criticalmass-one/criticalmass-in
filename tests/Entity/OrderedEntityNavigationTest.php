<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\Value;
use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilder;
use MalteHuebner\OrderedEntitiesBundle\SortOrder;
use PHPUnit\Framework\TestCase;

/**
 * Regression test for the previous/next pagination on ride and photo pages.
 *
 * The navigation is powered by maltehuebner/ordered-entities-bundle. The bundle derives the
 * adjacent entity from #[OE\Identical], #[OE\Order] and #[OE\Boolean] attributes on the entity
 * properties. When those attributes are missing the bundle builds an empty Criteria and the
 * navigation escapes the current city — "previous ride in Rostock" ended up in Cologne.
 *
 * These tests assert the attributes are present and wired correctly by inspecting the Criteria
 * the bundle builds for a given entity. No database is required.
 */
class OrderedEntityNavigationTest extends TestCase
{
    public function testPreviousRideStaysWithinSameCity(): void
    {
        $city = (new City())->setCity('Rostock');
        $ride = (new Ride())
            ->setCity($city)
            ->setDateTime(new \DateTime('2026-05-29'))
            ->setEnabled(true);

        $criteria = (new CriteriaBuilder())->build($ride, SortOrder::ASC);
        $comparisons = $this->flattenComparisons($criteria->getWhereExpression());

        $cityComparison = $this->findComparison($comparisons, 'city');
        $this->assertNotNull($cityComparison, 'Previous-ride navigation must be scoped to the same city');
        $this->assertSame(Comparison::EQ, $cityComparison->getOperator());
        $this->assertSame($city, $this->unwrap($cityComparison->getValue()));

        $dateComparison = $this->findComparison($comparisons, 'dateTime');
        $this->assertNotNull($dateComparison);
        $this->assertSame(Comparison::LT, $dateComparison->getOperator(), 'Previous ride must lie earlier in time');

        $enabledComparison = $this->findComparison($comparisons, 'enabled');
        $this->assertNotNull($enabledComparison, 'Only enabled rides may be navigated to');
        $this->assertTrue($this->unwrap($enabledComparison->getValue()));

        $this->assertSame('ASC', $criteria->orderings()['dateTime']->value);
    }

    public function testNextRideStaysWithinSameCity(): void
    {
        $city = (new City())->setCity('Rostock');
        $ride = (new Ride())
            ->setCity($city)
            ->setDateTime(new \DateTime('2026-05-29'))
            ->setEnabled(true);

        $criteria = (new CriteriaBuilder())->build($ride, SortOrder::DESC);
        $comparisons = $this->flattenComparisons($criteria->getWhereExpression());

        $cityComparison = $this->findComparison($comparisons, 'city');
        $this->assertNotNull($cityComparison, 'Next-ride navigation must be scoped to the same city');
        $this->assertSame(Comparison::EQ, $cityComparison->getOperator());
        $this->assertSame($city, $this->unwrap($cityComparison->getValue()));

        $dateComparison = $this->findComparison($comparisons, 'dateTime');
        $this->assertNotNull($dateComparison);
        $this->assertSame(Comparison::GT, $dateComparison->getOperator(), 'Next ride must lie later in time');

        $this->assertSame('DESC', $criteria->orderings()['dateTime']->value);
    }

    public function testPreviousPhotoStaysWithinSameRide(): void
    {
        $ride = (new Ride())->setDateTime(new \DateTime('2026-05-29'));
        $photo = (new Photo())
            ->setRide($ride)
            ->setEnabled(true)
            ->setDeleted(false)
            ->setExifCreationDate(new \DateTime('2026-05-29 18:00:00'));

        $criteria = (new CriteriaBuilder())->build($photo, SortOrder::ASC);
        $comparisons = $this->flattenComparisons($criteria->getWhereExpression());

        $rideComparison = $this->findComparison($comparisons, 'ride');
        $this->assertNotNull($rideComparison, 'Photo navigation must be scoped to the same ride');
        $this->assertSame(Comparison::EQ, $rideComparison->getOperator());
        $this->assertSame($ride, $this->unwrap($rideComparison->getValue()));

        $this->assertNotNull($this->findComparison($comparisons, 'exifCreationDate'), 'Photos are ordered by capture date');

        $enabledComparison = $this->findComparison($comparisons, 'enabled');
        $this->assertNotNull($enabledComparison, 'Only enabled photos may be navigated to');
        $this->assertTrue($this->unwrap($enabledComparison->getValue()));

        $deletedComparison = $this->findComparison($comparisons, 'deleted');
        $this->assertNotNull($deletedComparison, 'Deleted photos must be excluded from navigation');
        $this->assertFalse($this->unwrap($deletedComparison->getValue()));
    }

    /**
     * Recursively collects all leaf {@see Comparison} nodes from a (possibly composite) Criteria expression.
     *
     * @return Comparison[]
     */
    private function flattenComparisons(?Expression $expression): array
    {
        if ($expression instanceof Comparison) {
            return [$expression];
        }

        if ($expression instanceof CompositeExpression) {
            $comparisons = [];

            foreach ($expression->getExpressionList() as $child) {
                $comparisons = array_merge($comparisons, $this->flattenComparisons($child));
            }

            return $comparisons;
        }

        return [];
    }

    /**
     * @param Comparison[] $comparisons
     */
    private function findComparison(array $comparisons, string $field): ?Comparison
    {
        foreach ($comparisons as $comparison) {
            if ($comparison->getField() === $field) {
                return $comparison;
            }
        }

        return null;
    }

    private function unwrap(mixed $value): mixed
    {
        return $value instanceof Value ? $value->getValue() : $value;
    }
}
