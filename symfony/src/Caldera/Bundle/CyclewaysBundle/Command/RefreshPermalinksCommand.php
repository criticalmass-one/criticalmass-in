<?php

namespace Caldera\Bundle\CyclewaysBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshPermalinksCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var OutputInterface $output */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('cycleways:permalink:refresh')
            ->setDescription('Refresh slugs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->memcache = $this->getContainer()->get('memcache.criticalmass');

        $incidents = $this->doctrine->getRepository('CalderaBundle:Incident')->findAll();

        foreach ($incidents as $incident) {
            $this->refreshPermalink($incident);
        }

        $this->manager->flush();
    }

    protected function refreshPermalink(Incident $incident)
    {
        $this->output->writeln(
            sprintf(
                'Incident <info>#%d</info> <comment>%s</comment>',
                $incident->getId(),
                $incident->getTitle()
            )
        );

        if (!$incident->getPermalink()) {
            $this->output->writeln(
                'There is currently no permalink'
            );
        }
    }
}