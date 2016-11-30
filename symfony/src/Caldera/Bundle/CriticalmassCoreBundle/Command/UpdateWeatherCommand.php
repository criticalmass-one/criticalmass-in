<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Weather;
use Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather\OpenWeatherQuery;
use Caldera\Bundle\CriticalmassCoreBundle\Weather\OpenWeather\OpenWeatherReader;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\CurrentWeather;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

class UpdateWeatherCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    protected $em;

    /** @var OpenWeatherReader $reader */
    protected $reader;

    /** @var OpenWeatherQuery $query */
    protected $query;

    /** @var OpenWeatherMap $owm */
    protected $owm;

    /** @var OutputInterface $output */
    protected $output;

    /** @var InputInterface $input */
    protected $input;

    protected function configure()
    {
        $this
            ->setName('criticalmass:weather:update')
            ->setDescription('Create rides for a parameterized year and month automatically');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->owm = new OpenWeatherMap($this->getContainer()->getParameter('openweather.appid'));

        $startDateTime = new \DateTime();
        $endDateInterval = new \DateInterval('P1W');
        $endDateTime = new \DateTime();
        $endDateTime->add($endDateInterval);
        $halfDayInterval = new \DateInterval('PT12H');
        $halfDateTime = new \DateTime();
        $halfDateTime->sub($halfDayInterval);

        $rides = $this->getContainer()->get('doctrine')->getRepository('CalderaBundle:Ride')->findRidesInInterval($startDateTime, $endDateTime);
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln('Looking for rides from ' . $startDateTime->format('Y-m-d') . ' to ' . $endDateTime->format('Y-m-d'));

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            /** @var Weather $currentWeather */
            $currentWeather = $this->getContainer()->get('doctrine')->getRepository('CalderaBundle:Weather')->findCurrentWeatherForRide($ride);

            if (!$currentWeather || $currentWeather->getCreationDateTime() < $halfDateTime) {
                $this->retrieveWeather($ride);

                $this->output->writeln('Ride: ' . $ride->getFancyTitle() . ' (' . $ride->getDateTime()->format('Y-m-d H:i:s') . '): gespeichert');
            } else {
                $this->retrieveWeather($ride);

                $this->output->writeln('Ride: ' . $ride->getFancyTitle() . ' (' . $ride->getDateTime()->format('Y-m-d H:i:s') . '): existiert bereits');
            }
        }
    }

    protected function retrieveWeather(Ride $ride)
    {
        $coord = [
            'lat' => $ride->getLatitude(),
            'lon' => $ride->getLongitude()
        ];

        try {
            /** @var CurrentWeather $owmWeather */
            $owmWeather = $this->owm->getWeather($coord, 'metric', 'de');

            $weather = $this->createWeatherEntity($ride, $owmWeather);
        } catch(OWMException $e) {
            echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        } catch(\Exception $e) {
            echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        }


        /*
        $result = $this->query->setRide($ride)->execute();

        $this->reader->setJson($result);
        $this->reader->setDate($ride->getDateTime());

        $entity = $this->reader->createEntity();

        if ($entity) {
            $entity->setRide($ride);

            $this->em->persist($entity);
            $this->em->flush();
        }*/
    }

    protected function createWeatherEntity(Ride $ride, CurrentWeather $owmWeather): Weather
    {
        $weather = new Weather();

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime())
            ->setJson($owmWeather)

            ->setTemperatureMin($owmWeather->temperature->min->getValue())
            ->setTemperatureMax($owmWeather->temperature->max->getValue())
            ->setTemperatureMorning($owmWeather->temperature->morning->getValue())
            ->setTemperatureDay($owmWeather->temperature->day->getValue())
            ->setTemperatureEvening($owmWeather->temperature->evening->getValue())
            ->setTemperatureNight($owmWeather->temperature->night->getValue())

            ->setWeather($owmWeather->weather->)
            ->setWeatherDescription($owmWeather->weather->description)
            ->setWeatherCode($owmWeather->weather->id)
            ->setWeatherIcon($owmWeather->weather->icon)

            ->setPressure($owmWeather->pressure->getValue())
            ->setHumidity($owmWeather->humidity->getValue())

            ->setWindSpeed($owmWeather->wind->speed->getValue())
            ->setWindDeg($owmWeather->wind->direction->getValue())

            ->setClouds($owmWeather->clouds->getValue())
            ->setRain($owmWeather->precipitation->getValue())
        ;

        return $weather;
    }
}