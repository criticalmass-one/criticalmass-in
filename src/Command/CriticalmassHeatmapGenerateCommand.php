<?php declare(strict_types=1);

namespace App\Command;

use App\Criticalmass\Heatmap\Generator\HeatmapGenerator;
use App\Entity\Heatmap;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CriticalmassHeatmapGenerateCommand extends Command
{
    protected static $defaultName = 'criticalmass:heatmap:generate';

    /** @var HeatmapGenerator $heatmapGenerator */
    protected $heatmapGenerator;

    /** @var RegistryInterface $registry */
    protected $registry;

    protected function configure()
    {
        $this
            ->setDescription('Generate heatmap')
            ->addArgument('identifier', InputArgument::REQUIRED, 'Heatmap identifier');
        ;
    }

    public function __construct(string $name = null, HeatmapGenerator $heatmapGenerator, RegistryInterface $registry)
    {
        $this->heatmapGenerator = $heatmapGenerator;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $heatmap = $this->registry->getRepository(Heatmap::class)->findOneByIdentifier($input->getArgument('identifier'));

        $this->heatmapGenerator->setHeatmap($heatmap)->generate();
    }
}
