<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Criticalmass\Image\PhotoGps\PhotoGpsInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RelocatePhotosCommand extends Command
{
    protected static $defaultName = 'criticalmass:photos:relocate';
    public function __construct(protected PhotoGpsInterface $photoGps, protected ManagerRegistry $registry)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Relocate photos to tracks')
            ->addArgument(
                'citySlug',
                InputArgument::REQUIRED,
                'Slug of the city'
            )
            ->addArgument(
                'rideDate',
                InputArgument::REQUIRED,
                'date of the ride'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Id of the user'
            )
            ->addArgument(
                'photoDateTimeZone',
                InputArgument::OPTIONAL,
                'Timezone of the photos datetime values'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasArgument('photoDateTimeZone') && $input->getArgument('photoDateTimeZone')) {
            $dateTimeZone = new \DateTimeZone($input->getArgument('photoDateTimeZone'));

            $this->photoGps->setDateTimeZone($dateTimeZone);
        }

        /** @var Ride $ride */
        $ride = $this->registry->getRepository(Ride::class)->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));

        /** @var User $user */
        $user = $this->registry->getRepository(User::class)->findOneByUsername($input->getArgument('username'));

        /** @var Track $track */
        $track = $this->registry->getRepository(Track::class)->findByUserAndRide($ride, $user);

        $this->photoGps->setTrack($track);

        $photoList = $this->registry->getRepository(Photo::class)->findPhotosByUserAndRide($user, $ride);

        $table = new Table($output);
        $table->setHeaders([
            'Id',
            'DateTime',
            'Latitude',
            'Longitude',
            'Location',
        ]);

        $progressBar = new ProgressBar($output, is_countable($photoList) ? count($photoList) : 0);

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $this->photoGps
                ->setPhoto($photo)
                ->execute();

            $table->addRow([
                $photo->getId(),
                $photo->getExifCreationDate()->format('Y-m-d H:i:s'),
                $photo->getLatitude(),
                $photo->getLongitude(),
                $photo->getLocation(),
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->registry->getManager()->flush();
        $table->render();
    }
}
