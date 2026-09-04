<?php declare(strict_types=1);

namespace App\Command\SocialNetwork;

use App\Criticalmass\SocialNetwork\FeedsApi\FeedItemProviderInterface;
use App\Repository\SocialNetworkProfileRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'criticalmass:social-network:warm-feeds-cache',
    description: 'Refreshes the cached feed items for every city and the timeline',
)]
class WarmFeedsCacheCommand extends Command
{
    public function __construct(
        private readonly FeedItemProviderInterface $feedItemProvider,
        private readonly SocialNetworkProfileRepository $profileRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('cities-only', null, InputOption::VALUE_NONE, 'Skip the timeline windows');
        $this->addOption('timeline-only', null, InputOption::VALUE_NONE, 'Skip the city pages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $failures = 0;

        if (!$input->getOption('cities-only')) {
            $failures += $this->warmTimeline($io);
        }

        if (!$input->getOption('timeline-only')) {
            $failures += $this->warmCities($io);
        }

        if ($failures > 0) {
            $io->warning(sprintf('%d entries could not be refreshed', $failures));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function warmTimeline(SymfonyStyle $io): int
    {
        $failures = 0;

        foreach ($this->timelineWindows() as $label => [$since, $until]) {
            try {
                $items = $this->feedItemProvider->getTimelineItems(
                    since: $since,
                    until: $until,
                    refresh: true,
                );

                $io->writeln(sprintf('timeline %s: %d items', $label, count($items)));
            } catch (\Throwable $exception) {
                $io->error(sprintf('timeline %s failed: %s', $label, $exception->getMessage()));
                ++$failures;
            }
        }

        return $failures;
    }

    private function warmCities(SymfonyStyle $io): int
    {
        $cities = $this->profileRepository->findCitiesWithFeedsProfiles();

        $io->writeln(sprintf('Warming %d cities', count($cities)));

        $failures = 0;
        $itemCount = 0;

        $progressBar = $io->createProgressBar(count($cities));

        foreach ($cities as $city) {
            try {
                $itemCount += count($this->feedItemProvider->getFeedItemsForCity($city, refresh: true));
            } catch (\Throwable $exception) {
                $io->error(sprintf('city %s failed: %s', $city->getCity(), $exception->getMessage()));
                ++$failures;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $io->newLine(2);
        $io->writeln(sprintf('%d cities warmed, %d items cached', count($cities) - $failures, $itemCount));

        return $failures;
    }

    /**
     * The windows the pages actually ask for: the front page shows the last
     * month, /timeline the current one, and the month before that is one click
     * away. They are day-aligned by the provider, so warming them here hits the
     * same cache entries a visitor will.
     *
     * @return array<string, array{\DateTimeImmutable, \DateTimeImmutable}>
     */
    private function timelineWindows(): array
    {
        $now = new \DateTimeImmutable();
        $currentMonth = $now->modify('first day of this month');
        $previousMonth = $currentMonth->modify('-1 month');

        return [
            'front page' => [$now->modify('-1 month'), $now],
            'current month' => [$currentMonth, $currentMonth->modify('last day of this month')],
            'previous month' => [$previousMonth, $previousMonth->modify('last day of this month')],
        ];
    }
}
