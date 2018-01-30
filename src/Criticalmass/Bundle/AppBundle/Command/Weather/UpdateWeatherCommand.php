<?php

namespace Criticalmass\Bundle\AppBundle\Command\Weather;

use Criticalmass\Bundle\AppBundle\Entity\Weather;
use Criticalmass\Component\Weather\WeatherForecastRetriever;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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
            ->setDescription('Create rides for a parameterized year and month automatically');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->weatherForecastRetriever->retrieve();

        $newForecasts = $this->weatherForecastRetriever->getNewWeatherForecasts();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'DateTime'])
        ;

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
