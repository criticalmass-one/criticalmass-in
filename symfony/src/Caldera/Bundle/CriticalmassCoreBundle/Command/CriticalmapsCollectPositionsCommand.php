<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Position;
use Curl\Curl;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CriticalmapsCollectPositionsCommand extends ContainerAwareCommand
{
    /** @var InputInterface $input */
    protected $input;

    /** @var OutputInterface $output */
    protected $output;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var string $accessToken */
    protected $accessToken;

    protected function configure()
    {
        $this
            ->setName('criticalmass:criticalmaps:collect-positions')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->manager = $this->getContainer()->get('doctrine')->getManager();

        $locations = $this->fetchLocations();

        $this->savePositions($locations);

        $this->manager->flush();
    }

    protected function fetchLocations()
    {
        $curl = new Curl();
        $curl->get('http://api.criticalmaps.net/get');

        $result = json_decode($curl->response);

        $locations = $result->locations;

        return $locations;
    }

    protected function savePositions($locations)
    {
        foreach ($locations as $identifier => $location) {
            $position = $this->convertLocationToPosition($location);

            $this->output->writeln(sprintf('Position [<info>%f</info>, <info>%f</info>] saved', $position->getLatitude(), $position->getLongitude()));

            $ride = $this->findRideForPosition($position);
            $criticalmapsUser = $this->findCriticalmapsUserForIdentifier($identifier);

            if ($ride) {
                echo $ride->getCity()->getCity();
            } else {
                echo "NÖ";
            }
        }
    }

    protected function convertLocationToPosition($location): Position
    {
        $latitude = $location->latitude / 1000000;
        $longitude = $location->longitude / 1000000;
        $timestamp = $location->timestamp;
        $dateTime = new \DateTime();

        $position = new Position();
        $position
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setTimestamp($timestamp)
            ->setCreationDateTime($dateTime);

        return $position;
    }

    protected function findRideForPosition(Position $position)
    {
        $finder = $this->getContainer()->get('fos_elastica.finder.criticalmass.ride');

        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $position->getLatitude(),
                'lon' => $position->getLongitude()
            ],
            '30km'
        );

        $dateTimeFilter = new \Elastica\Filter\Term(['simpleDate' => '2016-06-24']);

        $filter = new \Elastica\Filter\BoolAnd([$geoFilter, $dateTimeFilter]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $filter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(1);
        $query->setSort(
            [
                '_geo_distance' =>
                    [
                        'pin' =>
                            [
                                $position->getLatitude(),
                                $position->getLongitude()
                            ],
                        'order' => 'asc',
                        'unit' => 'km'
                    ]
            ]
        );

        $results = $finder->find($query);

        echo count($results);
        return array_pop($results);
    }

    protected function findCriticalmapsUserForIdentifier(string $identifier)
    {

    }
}