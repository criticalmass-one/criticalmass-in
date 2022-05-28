<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TracksTransformCommand extends Command
{
    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:tracks:transform')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tracks = $this->registry->getRepository(Track::class)->findAll();

        $em = $this->registry->getManager();

        /** @var Track $track */
        foreach ($tracks as $track) {
            $output->writeln('Track #' . $track->getId());

            $array = json_decode($track->getLatLngList());

            if (is_array($array) && count($array) > 0) {
                $polyline = \Polyline::Encode($array);

                $track->setPolyline($polyline);

                $output->writeln($polyline);

                $em->persist($track);
            }
        }

        $em->flush();
    }
}
