<?php declare(strict_types=1);

namespace App\Command\Heatmap;

use App\Criticalmass\Heatmap\HeatmapFactory\HeatmapFactoryInterface;
use App\Entity\CitySlug;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    protected static $defaultName = 'criticalmass:heatmap:create';

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var HeatmapFactoryInterface $heatmapFactory */
    protected $heatmapFactory;

    protected function configure(): void
    {
        $this
            ->setDescription('Create heatmap')
            ->addArgument('city-slug', InputArgument::REQUIRED, 'City slug')
            ->addArgument('ride-identifier', InputArgument::OPTIONAL, 'Ride identifier');
    }

    public function __construct(string $name = null, RegistryInterface $registry, HeatmapFactoryInterface $heatmapFactory)
    {
        $this->registry = $registry;
        $this->heatmapFactory = $heatmapFactory;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $citySlugString = $input->getArgument('city-slug');
        $rideIdentifier = $input->getArgument('ride-identifier');

        if ($rideIdentifier) {
            $ride = $this->registry->getRepository(Ride::class)->findOneByCitySlugAndSlug($citySlugString, $rideIdentifier);

            $this->heatmapFactory->withRide($ride);
        } else {
            /** @var CitySlug $citySlug */
            $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($citySlugString);

            $this->heatmapFactory->withCity($citySlug->getCity());
        }

        $heatmap = $this->heatmapFactory->build();

        $manager = $this->registry->getManager();

        $manager->persist($heatmap);
        $manager->flush();

        $output->writeln(sprintf('Created new heatmap <info>%s</info>', $heatmap->getIdentifier()));
    }
}
