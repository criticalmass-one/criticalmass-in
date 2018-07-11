<?php declare(strict_types=1);

namespace AppBundle\Command\Geocoding;

use AppBundle\Entity\Photo;
use Geocoder\Query\ReverseQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeocodingCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(?string $name = null, RegistryInterface $registry)
    {
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:geocoding:photos')
            ->setDescription('Geocode photos')
            ->addOption('sleep', 's', InputArgument::OPTIONAL, 'Delay between nominatim queries, must not be lower than 1 second', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $sleep = (int) $input->getOption('sleep');
        $photoList = $this->registry->getRepository(Photo::class)->findGeocodeablePhotos();

        $httpClient = new \Http\Adapter\Guzzle6\Client();
        $provider = new \Geocoder\Provider\Nominatim\Nominatim($httpClient, 'https://nominatim.openstreetmap.org', 'Critical Mass Photo Geocoder', 'https://criticalmass.in/');
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');

        foreach ($photoList as $photo) {
            $result = $geocoder->reverseQuery(ReverseQuery::fromCoordinates($photo->getLatitude(), $photo->getLongitude()));

            $firstResult = $result->first();

            var_dump($firstResult->getStreetName(), $firstResult->getAdminLevels()->first()->getName());

            sleep($sleep);
        }


    }
}
