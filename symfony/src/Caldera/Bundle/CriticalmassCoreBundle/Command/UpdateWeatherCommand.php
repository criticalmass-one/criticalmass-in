<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather\OpenWeatherQuery;
use Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather\OpenWeatherReader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateWeatherCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:weather:update')
            ->setDescription('Create rides for a parameterized year and month automatically')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rides = $this->getContainer()->get('doctrine')->getRepository('CalderaCriticalmassModelBundle:Ride')->findCurrentRides();
        $em = $this->getContainer()->get('doctrine')->getManager();
        $query = new OpenWeatherQuery();
        $reader = new OpenWeatherReader();

        foreach ($rides as $ride) {
            $result = $query->setRide($ride)->execute();

            $reader->setJson($result);
            $reader->setDate($ride->getDateTime());

            $entity = $reader->createEntity();

            if ($entity) {
                $entity->setRide($ride);

                $em->persist($entity);
                $em->flush();

                $output->writeln($ride->getFancyTitle());
            }
        }


    }
}