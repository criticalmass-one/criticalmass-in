<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather\OpenWeatherQuery;
use Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather\OpenWeatherReader;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Weather;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateWeatherCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var OpenWeatherReader $reader
     */
    protected $reader;

    /**
     * @var OpenWeatherQuery $query
     */
    protected $query;

    protected function configure()
    {
        $this
            ->setName('criticalmass:weather:update')
            ->setDescription('Create rides for a parameterized year and month automatically')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startDateTime = new \DateTime();
        $endDateInterval = new \DateInterval('P4D');
        $endDateTime = new \DateTime();
        $endDateTime->add($endDateInterval);
        $halfDayInterval = new \DateInterval('PT12H');
        $halfDateTime = new \DateTime();
        $halfDateTime->sub($halfDayInterval);

        $rides = $this->getContainer()->get('doctrine')->getRepository('CalderaBundle:Ride')->findRidesInInterval($startDateTime, $endDateTime);
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->query = $this->getContainer()->get('caldera.criticalmass.weather.openweather.query');
        $this->reader = $this->getContainer()->get('caldera.criticalmass.weather.openweather.reader');

        $output->writeln('Looking for rides from '.$startDateTime->format('Y-m-d').' to '.$endDateTime->format('Y-m-d'));

        foreach ($rides as $ride) {
            /**
             * @var Weather $currentWeather
             */
            $currentWeather = $this->getContainer()->get('doctrine')->getRepository('CalderaBundle:Weather')->findCurrentWeatherForRide($ride);

            if (!$currentWeather or $currentWeather->getCreationDateTime() < $halfDateTime) {
                $this->retrieveWeather($ride);

                $output->writeln('Ride: '.$ride->getFancyTitle().' ('.$ride->getDateTime()->format('Y-m-d H:i:s').'): gespeichert');
            } else {
                $output->writeln('Ride: '.$ride->getFancyTitle().' ('.$ride->getDateTime()->format('Y-m-d H:i:s').'): existiert bereits');
            }
        }
    }

    protected function retrieveWeather(Ride $ride)
    {
        $result = $this->query->setRide($ride)->execute();

        $this->reader->setJson($result);
        $this->reader->setDate($ride->getDateTime());

        $entity = $this->reader->createEntity();

        if ($entity) {
            $entity->setRide($ride);

            $this->em->persist($entity);
            $this->em->flush();
        }
    }
}