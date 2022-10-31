<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Criticalmass\Image\ExifHandler\ExifHandlerInterface;
use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssignExifCommand extends Command
{
    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var ExifHandlerInterface $exifHandler */
    protected $exifHandler;

    public function __construct(ManagerRegistry $registry, ExifHandlerInterface $exifHandler)
    {
        $this->registry = $registry;
        $this->exifHandler = $exifHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:photos:assign-exif')
            ->setDescription('Assign exif data to photos')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of photos to process at once')
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Offset to start processing')
            ->addOption('overwrite', 'ow', InputOption::VALUE_NONE, 'Overwrite existing data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;
        $offset = $input->getOption('offset') ? (int) $input->getOption('offset') : null;
        $overwrite = $input->getOption('overwrite') ? (bool) $input->getOption('overwrite') : false;

        $photoList = $this->registry->getRepository(Photo::class)->findPhotosWithoutExifData($limit, $offset, $overwrite);

        $progressBar = new ProgressBar($output, count($photoList));

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $exif = $this->exifHandler->readExifDataFromPhotoFile($photo);

            if ($exif) {
                $this->exifHandler->assignExifDataToPhoto($photo, $exif);
            }

            $progressBar->advance();
        }

        $this->registry->getManager()->flush();

        $progressBar->finish();
    }
}
