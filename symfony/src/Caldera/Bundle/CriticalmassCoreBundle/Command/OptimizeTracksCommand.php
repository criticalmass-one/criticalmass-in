<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\StandardRideGenerator\StandardRideGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StravaImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:import:strava')
            ->setDescription('Import rides from Strava')
            /*->addArgument(
                'year',
                InputArgument::REQUIRED,
                'Year of the rides to create'
            )
            ->addArgument(
                'month',
                InputArgument::REQUIRED,
                'Month of the rides to create'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Use to create the rides, otherwise you only get a preview')*/
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = new \Iamstuartwilson\StravaApi(
            $clientId,
            $clientSecret
        );
    }
}