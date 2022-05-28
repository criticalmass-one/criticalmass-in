<?php declare(strict_types=1);

namespace App\Command\Geocoding;

use App\Criticalmass\Geocoding\ReverseGeocoderInterface;
use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeocodingCommand extends Command
{
    protected ManagerRegistry $registry;
    protected ReverseGeocoderInterface $reverseGeocoder;

    public function __construct(ManagerRegistry $registry, ReverseGeocoderInterface $reverseGeocoder)
    {
        $this->registry = $registry;

        $this->reverseGeocoder = $reverseGeocoder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:geocoding:photos')
            ->setDescription('Geocode photos')
            ->addOption('sleep', 's', InputArgument::OPTIONAL, 'Delay between nominatim queries, must not be lower than 1 second', 2.5)
            ->addOption('limit', 'l', InputArgument::OPTIONAL, 'Max number of photos per command call', 50);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $sleep = (int) $input->getOption('sleep');
        $limit = (int) $input->getOption('limit');

        $photoList = $this->registry->getRepository(Photo::class)->findGeocodeablePhotos($limit, true);

        $progressBar = new ProgressBar($output, count($photoList));
        $table = new Table($output);

        $table->setHeaders([
            'Photo Id',
            'DateTime',
            'City',
            'Ride DateTime',
            'Location',
        ]);

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $photo = $this->reverseGeocoder->reverseGeocode($photo);

            $table->addRow([
                $photo->getId(),
                $photo->getExifCreationDate()->format('Y-m-d H:i:s'),
                $photo->getRide()->getCity()->getCity(),
                $photo->getRide()->getDateTime()->format('Y-m-d'),
                $photo->getLocation(),
            ]);

            $progressBar->advance();

            sleep($sleep);
        }

        $this->registry->getManager()->flush();
        $progressBar->finish();
        $table->render();
    }
}
