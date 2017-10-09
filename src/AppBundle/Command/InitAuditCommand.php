<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Entity\Location;
use AppBundle\Entity\Region;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Subride;
use AppBundle\EntityInterface\AuditableInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitAuditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:audit:init')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $manager = $doctrine->getManager();

        $classnames = [City::class, Ride::class, Subride::class, Region::class, Location::class];

        foreach ($classnames as $classname) {

            $entities = $this->findEntities($doctrine, $classname);

            $output->writeln(sprintf('Init <info>%d</info> entities of <comment>%s</comment> now', count($entities), $classname));
            $progress = new ProgressBar($output, count($entities));

            foreach ($entities as $entity) {
                $this->initEntity($manager, $entity);

                $progress->advance();
            }

            $manager->flush();

            $progress->finish();
        }
    }

    protected function findEntities(Doctrine $doctrine, string $classname): array
    {
        return $doctrine->getRepository($classname)->findAll();
    }

    protected function initEntity(ObjectManager $manager, AuditableInterface $auditable): void
    {
        $manager->detach($auditable);
        $manager->persist($auditable);
    }
}
