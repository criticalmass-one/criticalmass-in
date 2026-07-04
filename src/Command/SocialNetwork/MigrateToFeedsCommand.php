<?php declare(strict_types=1);

namespace App\Command\SocialNetwork;

use App\Criticalmass\SocialNetwork\FeedsApi\FeedsApiClientInterface;
use App\Entity\SocialNetworkProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'criticalmass:social-network:migrate-to-feeds',
    description: 'Migrate existing social network profiles to the Feeds API',
)]
class MigrateToFeedsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FeedsApiClientInterface $feedsApiClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Only show what would be done');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        $profiles = $this->entityManager->getRepository(SocialNetworkProfile::class)->findBy([
            'enabled' => true,
        ]);

        $io->info(sprintf('Found %d enabled profiles to migrate', count($profiles)));

        $success = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($profiles as $profile) {
            if ($profile->getFeedsProfileId()) {
                $io->note(sprintf('Profile #%d (%s) already has feeds_profile_id=%d, skipping', $profile->getId(), $profile->getIdentifier(), $profile->getFeedsProfileId()));
                $skipped++;
                continue;
            }

            if (!$profile->getIdentifier() || !$profile->getNetwork()) {
                $io->warning(sprintf('Profile #%d has no identifier or network, skipping', $profile->getId()));
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $io->info(sprintf('Would create feeds profile for #%d: %s (%s)', $profile->getId(), $profile->getIdentifier(), $profile->getNetwork()));
                $success++;
                continue;
            }

            try {
                $feedsProfile = $this->feedsApiClient->createProfile(
                    $profile->getIdentifier(),
                    $profile->getNetwork(),
                );

                $profile->setFeedsProfileId($feedsProfile->getId());
                $this->entityManager->flush();

                $io->success(sprintf('Profile #%d -> feeds_profile_id=%d', $profile->getId(), $feedsProfile->getId()));
                $success++;
            } catch (\Exception $e) {
                $io->error(sprintf('Failed for profile #%d (%s): %s', $profile->getId(), $profile->getIdentifier(), $e->getMessage()));
                $failed++;
            }
        }

        $io->section('Summary');
        $io->listing([
            sprintf('Success: %d', $success),
            sprintf('Skipped: %d', $skipped),
            sprintf('Failed: %d', $failed),
        ]);

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
