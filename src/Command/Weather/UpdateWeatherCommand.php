<?php

namespace AppBundle\Command\Weather;

use AppBundle\Entity\Weather;
use AppBundle\Criticalmass\Weather\WeatherForecastRetriever;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateWeatherCommand extends Command
{
    /** @var WeatherForecastRetriever $weatherForecastRetriever */
    protected $weatherForecastRetriever;

    /** @var OutputInterface $output */
    protected $output;

    /** @var InputInterface $input */
    protected $input;

    public function __construct(WeatherForecastRetriever $weatherForecastRetriever)
    {
        $this->weatherForecastRetriever = $weatherForecastRetriever;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:weather:update')
            ->setDescription('Retrieve weather forecasts for parameterized range')
            ->addArgument(
                'startDateTime',
                InputArgument::OPTIONAL,
                'Range start date time'
            )
            ->addArgument(
                'endDateTime',
                InputArgument::OPTIONAL,
                'Range end date time'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startDateTime = null;
        $endDateTime = null;

        if ($input->getArgument('startDateTime')) {
            $startDateTime = new \DateTime($input->getArgument('startDateTime'));
        }

        if ($input->getArgument('endDateTime')) {
            $endDateTime = new \DateTime($input->getArgument('endDateTime'));
        }

        $this->weatherForecastRetriever->retrieve($startDateTime, $endDateTime);

        $newForecasts = $this->weatherForecastRetriever->getNewWeatherForecasts();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'DateTime']);

        /** @var Weather $weather */
        foreach ($newForecasts as $weather) {
            $table
                ->addRow([
                    $weather->getRide()->getCity()->getCity(),
                    $weather->getRide()->getDateTime()->format('Y-m-d'),
                ]);
        }

        $table->render();
    }
}
