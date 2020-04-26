<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Command;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetectorInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use App\Entity\SocialNetworkProfile;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AutoAssignNetworkCommand extends Command
{
    protected ManagerRegistry $doctrine;
    protected NetworkDetectorInterface $networkDetector;
    protected NetworkManagerInterface $networkManager;

    public function __construct(ManagerRegistry $doctrine, NetworkManagerInterface $networkManager, NetworkDetectorInterface $networkDetector)
    {
        $this->doctrine = $doctrine;
        $this->networkDetector = $networkDetector;
        $this->networkManager = $networkManager;

        parent::__construct(null);

    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:social-network:auto-assign')
            ->setDescription('Auto-assign networks')
            ->addOption('only-diffs', null, InputOption::VALUE_NONE)
            ->addOption('auto-assign', null, InputOption::VALUE_NONE)
            ->addOption('interactive-assign', null, InputOption::VALUE_NONE)
            ->addOption('filter-old', null, InputOption::VALUE_REQUIRED)
            ->addOption('filter-detected', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $networkList = $this->createNetworkList();

        $profiles = $this->doctrine->getRepository(SocialNetworkProfile::class)->findAll();

        $table = new Table($output);
        $table->setHeaders([
            'Id',
            'Identifier',
            'Saved Network',
            'Detected Network',
        ]);

        /** @var SocialNetworkProfile $profile */
        foreach ($profiles as $profile) {
            $detectedNetwork = $this->networkDetector->detect($profile->getIdentifier());

            if ($input->getOption('filter-old') && $input->getOption('filter-old') !== $profile->getNetwork()) {
                continue;
            }

            if ($detectedNetwork && $input->getOption('filter-detected') && $input->getOption('filter-detected') !== $detectedNetwork->getIdentifier()) {
                continue;
            }

            if ($detectedNetwork && $detectedNetwork->getIdentifier() === $profile->getNetwork() && $input->getOption('only-diffs')) {
                continue;
            }

            if ($detectedNetwork && $detectedNetwork->getIdentifier() !== $profile->getNetwork() && $input->getOption('auto-assign')) {
                $profile->setNetwork($detectedNetwork->getIdentifier());
            }

            if ($detectedNetwork && $input->getOption('interactive-assign')) {
                $this->queryForNewNetwork($input, $output, $profile, $networkList, $detectedNetwork);
            }

            $table->addRow([
                $profile->getId(),
                $profile->getIdentifier(),
                $profile->getNetwork(),
                $detectedNetwork ? $detectedNetwork->getIdentifier() : 'unkown',
            ]);
        }

        if ($input->getOption('auto-assign') || $input->getOption('interactive-assign')) {
            $this->doctrine->getManager()->flush();
        }

        $table->render();
    }

    protected function queryForNewNetwork(InputInterface $input, OutputInterface $output, SocialNetworkProfile $socialNetworkProfile, array $networkList, NetworkInterface $detectedNetwork): void
    {
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            sprintf('Current assigned network is <info>%s</info>, identifier is <comment>%s</comment>, it looks like <info>%s</info>',
                $socialNetworkProfile->getNetwork(),
                $socialNetworkProfile->getIdentifier(),
                $detectedNetwork->getIdentifier()
            ),
            $networkList,
            $detectedNetwork->getIdentifier(),
        );

        $question->setErrorMessage('Network <info>%s</info> is invalid.');

        $networkIdentifier = $helper->ask($input, $output, $question);

        $output->writeln(sprintf('Assigned network is <comment>%s</comment>', $networkIdentifier));

        $socialNetworkProfile->setNetwork($networkIdentifier);
    }

    protected function createNetworkList(): array
    {
        /** @var NetworkInterface $network */
        foreach ($this->networkManager->getNetworkList() as $network) {
            $networkList[$network->getIdentifier()] = $network->getName();
        }

        return $networkList;
    }
}
