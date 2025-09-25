<?php

namespace App\Command;

use App\Criticalmass\Activity\ActivityCalculatorInterface;
use App\Entity\City;
use CalendR\Event\Manager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CalculateActivityIndexCommand extends Command
{
    protected static $defaultName = 'activity:calculate';
    protected static $defaultDescription = 'Add a short description for your command';
    protected ActivityCalculatorInterface $activityCalculator;
    protected ManagerRegistry $managerRegistry;

    public function __construct(string $name = null, ActivityCalculatorInterface $activityCalculator, ManagerRegistry $managerRegistry)
    {
        $this->activityCalculator = $activityCalculator;
        $this->managerRegistry = $managerRegistry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cityList = $this->managerRegistry->getRepository(City::class)->findAll();

        $io->progressStart(count($cityList));

        /** @var City $city */
        foreach ($cityList as $city) {
            $activityIndex = $this->activityCalculator->calculate($city);

            $city->setActivityIndex($activityIndex);

            $io->progressAdvance();
        }

        $io->progressFinish();


        $io->table(['City name', 'Activity index'], array_($cityList, function($city) {
            return [$city->getCity(), $city->getActivityIndex()];
        }));

        return 0;
    }
}
