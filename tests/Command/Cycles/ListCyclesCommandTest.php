<?php declare(strict_types=1);

namespace Tests\Command\Cycles;

use App\Command\Cycles\ListCyclesCommand;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\CitySlug;
use App\Repository\CityCycleRepository;
use App\Repository\CitySlugRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Support\EntityIdHelper;

final class ListCyclesCommandTest extends TestCase
{
    private City $city;

    /**
     * @param list<CityCycle> $cycles
     */
    private function tester(array $cycles, ?string $knownSlug = 'hamburg'): CommandTester
    {
        $this->city = (new City())->setCity('Hamburg');
        $citySlug = (new CitySlug())->setSlug('hamburg')->setCity($this->city);

        $citySlugRepository = $this->createMock(CitySlugRepository::class);
        $citySlugRepository->method('__call')->willReturnCallback(
            static fn (string $method, array $arguments): ?CitySlug => $arguments[0] === $knownSlug ? $citySlug : null
        );

        $cycleRepository = $this->createMock(CityCycleRepository::class);
        $cycleRepository->method('findByCity')->with($this->city)->willReturn($cycles);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturnMap([
            [CitySlug::class, null, $citySlugRepository],
            [CityCycle::class, null, $cycleRepository],
        ]);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id): string => '[' . $id . ']');

        $application = new Application();
        $application->addCommand(new ListCyclesCommand($registry, $translator));

        return new CommandTester($application->find('criticalmass:cycles:list'));
    }

    #[Test]
    public function rendersOneTableRowPerCycle(): void
    {
        $cycle = (new CityCycle())
            ->setLocation('Rathausmarkt')
            ->setLatitude(53.55)
            ->setLongitude(9.99)
            ->setDayOfWeek(5)
            ->setWeekOfMonth(0)
            ->setTime(new \DateTime('17:00'))
            ->setValidFrom(new \DateTime('2020-01-01'))
            ->setValidUntil(new \DateTime('2030-12-31'));
        EntityIdHelper::setId($cycle, 42);

        $tester = $this->tester([$cycle]);
        $tester->execute(['citySlug' => 'hamburg']);

        $display = $tester->getDisplay();

        self::assertSame(0, $tester->getStatusCode());
        self::assertStringContainsString('42', $display);
        self::assertStringContainsString('Rathausmarkt (53.550000, 9.990000)', $display);
        self::assertStringContainsString('[cycle.event_date.day.5]', $display);
        self::assertStringContainsString('[cycle.event_date.month_week.0]', $display);
        self::assertStringContainsString('17:00', $display);
        self::assertStringContainsString('2020-01-01', $display);
        self::assertStringContainsString('2030-12-31', $display);
    }

    #[Test]
    public function missingOptionalValuesRenderAsEmptyCells(): void
    {
        $cycle = (new CityCycle())->setDayOfWeek(1)->setWeekOfMonth(1);
        EntityIdHelper::setId($cycle, 7);

        $tester = $this->tester([$cycle]);
        $tester->execute(['citySlug' => 'hamburg']);

        self::assertSame(0, $tester->getStatusCode());
        self::assertStringContainsString('| 7 ', $tester->getDisplay());
    }

    #[Test]
    public function cityWithoutCyclesRendersOnlyTheHeader(): void
    {
        $tester = $this->tester([]);
        $tester->execute(['citySlug' => 'hamburg']);

        self::assertSame(0, $tester->getStatusCode());
        self::assertStringContainsString('Valid until', $tester->getDisplay());
    }

    #[Test]
    public function unknownSlugIsReportedGracefully(): void
    {
        $tester = $this->tester([], null);

        $tester->execute(['citySlug' => 'atlantis']);

        self::assertSame(1, $tester->getStatusCode());
        self::assertStringContainsString('No city found with slug "atlantis"', $tester->getDisplay());
    }
}
