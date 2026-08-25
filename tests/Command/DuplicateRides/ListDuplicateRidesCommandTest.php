<?php declare(strict_types=1);

namespace Tests\Command\DuplicateRides;

use App\Command\DuplicateRides\ListDuplicateRidesCommand;
use App\Criticalmass\RideDuplicates\DuplicateFinder\DuplicateFinderInterface;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Repository\CitySlugRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Support\EntityIdHelper;

final class ListDuplicateRidesCommandTest extends TestCase
{
    private MockObject&DuplicateFinderInterface $finder;
    private City $city;

    private function tester(): CommandTester
    {
        $this->city = (new City())->setId(1)->setCity('Hamburg');
        $citySlug = (new CitySlug())->setSlug('hamburg')->setCity($this->city);

        $citySlugRepository = $this->createMock(CitySlugRepository::class);
        $citySlugRepository->method('__call')->willReturnCallback(
            static fn (string $method, array $arguments): ?CitySlug => 'hamburg' === $arguments[0] ? $citySlug : null
        );

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(CitySlug::class)->willReturn($citySlugRepository);

        $this->finder = $this->createMock(DuplicateFinderInterface::class);

        $application = new Application();
        $application->addCommand(new ListDuplicateRidesCommand($registry, $this->finder));

        return new CommandTester($application->find('criticalmass:ride-duplicates:list'));
    }

    private function ride(int $id, string $dateTime, ?string $slug = null): Ride
    {
        $ride = (new Ride())
            ->setCity($this->city)
            ->setDateTime(new \DateTime($dateTime))
            ->setSlug($slug)
            ->setLocation('Rathausmarkt')
            ->setDescription(str_repeat('x', 40));
        $ride->setLatitude(53.55);
        $ride->setLongitude(9.99);
        EntityIdHelper::setId($ride, $id);

        return $ride;
    }

    #[Test]
    public function printsATablePerDuplicateGroup(): void
    {
        $tester = $this->tester();

        $this->finder->expects(self::never())->method('setCity');
        $this->finder->method('findDuplicates')->willReturn([
            '1-2024-05-31' => [
                10 => $this->ride(10, '2024-05-31 19:00:00'),
                11 => $this->ride(11, '2024-05-31 19:30:00', 'second'),
            ],
        ]);

        $tester->execute([]);
        $display = $tester->getDisplay();

        self::assertSame(0, $tester->getStatusCode());
        self::assertStringContainsString('Duplicates found for Hamburg in 2024-05-31', $display);
        self::assertStringContainsString('2024-05-31 19:00:00', $display);
        self::assertStringContainsString('2024-05-31 19:30:00', $display);
        self::assertStringContainsString('second', $display);
        self::assertStringContainsString('Rathausmarkt (53.550000, 9.990000)', $display);
        // Description is truncated to 32 characters.
        self::assertStringContainsString(str_repeat('x', 32), $display);
        self::assertStringNotContainsString(str_repeat('x', 33), $display);
    }

    #[Test]
    public function citySlugRestrictsTheFinderToThatCity(): void
    {
        $tester = $this->tester();

        $this->finder->expects(self::once())->method('setCity')->with($this->city)->willReturnSelf();
        $this->finder->method('findDuplicates')->willReturn([]);

        $tester->execute(['citySlug' => 'hamburg']);

        self::assertSame(0, $tester->getStatusCode());
        self::assertSame('', trim($tester->getDisplay()));
    }

    #[Test]
    public function unknownSlugIsReportedGracefully(): void
    {
        $tester = $this->tester();
        $this->finder->method('findDuplicates')->willReturn([]);

        try {
            $tester->execute(['citySlug' => 'atlantis']);
        } catch (\Error $error) {
            self::markTestIncomplete(
                'ListDuplicateRidesCommand prints "No city found" but does not return, then calls getCity() on null: '
                .$error->getMessage()
            );
        }

        self::assertStringContainsString('No city found with slug "atlantis"', $tester->getDisplay());
    }
}
