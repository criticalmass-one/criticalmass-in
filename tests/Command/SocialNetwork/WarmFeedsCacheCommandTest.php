<?php declare(strict_types=1);

namespace Tests\Command\SocialNetwork;

use App\Command\SocialNetwork\WarmFeedsCacheCommand;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedItemProviderInterface;
use App\Entity\City;
use App\Repository\SocialNetworkProfileRepository;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[TestDox('WarmFeedsCacheCommand')]
class WarmFeedsCacheCommandTest extends TestCase
{
    private FeedItemProviderInterface&MockObject $feedItemProvider;
    private SocialNetworkProfileRepository&MockObject $profileRepository;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->feedItemProvider = $this->createMock(FeedItemProviderInterface::class);
        $this->profileRepository = $this->createMock(SocialNetworkProfileRepository::class);

        $application = new Application();
        $application->add(new WarmFeedsCacheCommand($this->feedItemProvider, $this->profileRepository));

        $this->commandTester = new CommandTester($application->find('criticalmass:social-network:warm-feeds-cache'));
    }

    #[TestDox('refreshes every city that has feeds profiles')]
    public function testRefreshesEveryCity(): void
    {
        $this->profileRepository->method('findCitiesWithFeedsProfiles')->willReturn([
            $this->createCity('Hamburg'),
            $this->createCity('Magdeburg'),
        ]);

        $this->feedItemProvider->expects($this->exactly(2))
            ->method('getFeedItemsForCity')
            ->with($this->isInstanceOf(City::class), 1, true)
            ->willReturn([]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->execute(['--cities-only' => true]));
        $this->assertStringContainsString('2 cities warmed', $this->commandTester->getDisplay());
    }

    #[TestDox('refreshes the three timeline windows the pages ask for')]
    public function testRefreshesTimelineWindows(): void
    {
        $this->feedItemProvider->expects($this->exactly(3))
            ->method('getTimelineItems')
            ->with(
                $this->isInstanceOf(\DateTimeInterface::class),
                $this->isInstanceOf(\DateTimeInterface::class),
                null,
                true,
            )
            ->willReturn([]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->execute(['--timeline-only' => true]));
    }

    #[TestDox('keeps going when a single city fails and reports failure')]
    public function testSurvivesASingleFailure(): void
    {
        $this->profileRepository->method('findCitiesWithFeedsProfiles')->willReturn([
            $this->createCity('Hamburg'),
            $this->createCity('Magdeburg'),
        ]);

        $calls = 0;
        $this->feedItemProvider->method('getFeedItemsForCity')
            ->willReturnCallback(function () use (&$calls): array {
                if (++$calls === 1) {
                    throw new \RuntimeException('Feeds API returned status 502');
                }

                return [];
            });

        $this->assertSame(Command::FAILURE, $this->commandTester->execute(['--cities-only' => true]));
        $this->assertSame(2, $calls);
        $this->assertStringContainsString('1 cities warmed', $this->commandTester->getDisplay());
    }

    private function createCity(string $name): City&MockObject
    {
        $city = $this->createMock(City::class);
        $city->method('getCity')->willReturn($name);

        return $city;
    }
}
