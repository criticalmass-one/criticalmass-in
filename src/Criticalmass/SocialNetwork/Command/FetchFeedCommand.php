<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Command;

use App\Criticalmass\SocialNetwork\FeedFetcher\FeedFetcher;
use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Criticalmass\SocialNetwork\FeedFetcher\FetchResult;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchFeedCommand extends Command
{
    protected FeedFetcher $feedFetcher;
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine, FeedFetcher $feedFetcher)
    {
        $this->doctrine = $doctrine;
        $this->feedFetcher = $feedFetcher;

        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:social-network:fetch-feed')
            ->setDescription('Fetch feeds')
            ->addArgument('networks', InputArgument::IS_ARRAY)
            ->addOption('fromDateTime', 'f', InputOption::VALUE_REQUIRED)
            ->addOption('untilDateTime', 'u', InputOption::VALUE_REQUIRED)
            ->addOption('includeOldItems', 'i', InputOption::VALUE_NONE)
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $fetchInfo = new FetchInfo();

        if ($input->hasArgument('networks')) {
            foreach ($input->getArgument('networks') as $networkIdentifier) {
                $fetchInfo->addNetwork($networkIdentifier);
            }
        }

        if ($input->getOption('count')) {
            $fetchInfo->setCount((int)$input->getOption('count'));
        }

        if ($input->getOption('fromDateTime')) {
            $fetchInfo->setFromDateTime(new \DateTime($input->getOption('fromDateTime')));
        }

        if ($input->getOption('untilDateTime')) {
            $fetchInfo->setUntilDateTime(new \DateTime($input->getOption('untilDateTime')));
        }

        if ($input->getOption('includeOldItems')) {
            $fetchInfo->setIncludeOldItems(true);
        }

        $callback = function (FetchResult $fetchResult) use ($io): void {
            $io->success(sprintf('Fetched %d items from profile %s', $fetchResult->getCounter(), $fetchResult->getSocialNetworkProfile()->getIdentifier()));
        };

        $this->feedFetcher
            ->fetch($fetchInfo, $callback)
            ->persist();
    }
}
