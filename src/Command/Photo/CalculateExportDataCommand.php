<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Criticalmass\Image\GoogleCloud\ExportDataHandler\ExportDataHandlerInterface;
use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateExportDataCommand extends Command
{
    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var ExportDataHandlerInterface $exportDataHandler */
    protected $exportDataHandler;

    public function __construct(ManagerRegistry $registry, ExportDataHandlerInterface $exportDataHandler)
    {
        $this->registry = $registry;
        $this->exportDataHandler = $exportDataHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:photos:calculate-export-data')
            ->setDescription('Calculate export data for photos')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of photos to process at once')
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Offset to start processing')
            ->addOption('overwrite', 'ow', InputOption::VALUE_NONE, 'Overwrite existing data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;
        $offset = $input->getOption('offset') ? (int) $input->getOption('offset') : null;
        $overwrite = $input->getOption('overwrite') ? (bool) $input->getOption('overwrite') : false;

        $photoList = $this->registry->getRepository(Photo::class)->findPhotosWithoutExportData($limit, $offset, $overwrite);

        $progressBar = new ProgressBar($output, count($photoList));

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $this->exportDataHandler->calculateForEntity($photo);

            $progressBar->advance();
        }

        $this->registry->getManager()->flush();

        $progressBar->finish();
    }
}
