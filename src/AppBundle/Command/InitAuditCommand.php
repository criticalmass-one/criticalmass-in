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
        $repo = $doctrine->getRepository('AppBundle:City');
        $manager = $doctrine->getManager();

        $classnames = [City::class, Ride::class, Subride::class, Region::class, Location::class];

        foreach ($classnames as $classname) {
            $entities = $this->findEntities($doctrine, $classname);

            foreach ($entities as $entity) {
                $this->initEntity($manager, $entity);
            }
        }

        $manager->flush();
    }

    protected function findEntities(Doctrine $doctrine, string $classname): array
    {
        return $doctrine->getRepository($classname)->findBy(['isArchived' => false]);
    }

    protected function initEntity(ObjectManager $manager, AuditableInterface $auditable): void
    {
        $manager->detach($auditable);
        $manager->persist($auditable);
    }
}