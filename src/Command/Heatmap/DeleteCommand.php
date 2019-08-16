<?php declare(strict_types=1);

namespace App\Command\Heatmap;

use App\Criticalmass\Heatmap\Remover\HeatmapRemoverInterface;
use App\Entity\Heatmap;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends Command
{
    protected static $defaultName = 'criticalmass:heatmap:delete';

    /** @var HeatmapRemoverInterface $heatmapRemover */
    protected $heatmapRemover;

    /** @var RegistryInterface $registry */
    protected $registry;

    protected function configure(): void
    {
        $this
            ->setDescription('Delete heatmap')
            ->addArgument('identifier', InputArgument::REQUIRED, 'Heatmap identifier')
            ->addOption('delete-tiles', 'dt', InputOption::VALUE_NONE, 'Only remove tiles and keep heatmap');
    }

    public function __construct(string $name = null, HeatmapRemoverInterface $heatmapRemover, RegistryInterface $registry)
    {
        $this->heatmapRemover = $heatmapRemover;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var Heatmap $heatmap */
        $heatmap = $this->registry->getRepository(Heatmap::class)->findOneByIdentifier($input->getArgument('identifier'));

        if (!$input->getOption('delete-tiles')) {
            $this->heatmapRemover->remove($heatmap);

            $output->writeln(sprintf('Removed heatmap <info>%s</info>', $heatmap->getIdentifier()));
        } else {
            $this->heatmapRemover->flush($heatmap);

            $output->writeln(sprintf('Flushed heatmap <info>%s</info>', $heatmap->getIdentifier()));
        }
    }
}
