<?php declare(strict_types=1);

namespace Tests\DQL;

use App\Entity\Ride;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The custom DQL functions are registered in config/packages/doctrine.yaml; this
 * checks they parse and translate to the expected SQL (no query is executed).
 */
final class DqlFunctionsTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * @return iterable<string, array{0: string, 1: string}>
     */
    public static function dateFunctions(): iterable
    {
        yield 'YEAR' => ['YEAR', 'YEAR('];
        yield 'MONTH' => ['MONTH', 'MONTH('];
        yield 'DAY' => ['DAY', 'DAY('];
        yield 'DATE' => ['DATE', 'DATE('];
        yield 'DAYOFWEEK' => ['DAYOFWEEK', 'DAYOFWEEK('];
    }

    #[Test]
    #[DataProvider('dateFunctions')]
    public function dateFunctionsWrapTheColumn(string $dqlFunction, string $sqlPrefix): void
    {
        $sql = $this->entityManager
            ->createQuery(sprintf('SELECT %s(r.dateTime) AS v FROM %s r', $dqlFunction, Ride::class))
            ->getSQL();

        self::assertMatchesRegularExpression('/SELECT ' . preg_quote($sqlPrefix, '/') . '\w+\.dateTime\) AS/', $sql);
    }

    #[Test]
    public function dateFunctionsCanBeUsedInWhereClausesWithParameters(): void
    {
        $sql = $this->entityManager
            ->createQuery(sprintf('SELECT r FROM %s r WHERE YEAR(r.dateTime) = :year AND MONTH(r.dateTime) = :month', Ride::class))
            ->getSQL();

        self::assertStringContainsString('WHERE YEAR(', $sql);
        self::assertStringContainsString(' AND MONTH(', $sql);
    }

    #[Test]
    public function randOrdersRandomly(): void
    {
        $sql = $this->entityManager
            ->createQuery(sprintf('SELECT r FROM %s r ORDER BY RAND()', Ride::class))
            ->getSQL();

        self::assertStringEndsWith('ORDER BY RAND() ASC', $sql);
    }

    #[Test]
    public function dateFunctionsRequireExactlyOneArgument(): void
    {
        $this->expectException(\Doctrine\ORM\Query\QueryException::class);

        $this->entityManager->createQuery(sprintf('SELECT YEAR(r.dateTime, r.createdAt) FROM %s r', Ride::class))->getSQL();
    }
}
