<?php

namespace Caldera\Bundle\CyclewaysBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CyclewaysBundle\PermalinkManager\SqibePermalinkManager;
use Caldera\Bundle\CyclewaysBundle\SlugGenerator\SlugGenerator;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class RefreshSlugsCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var OutputInterface $output */
    protected $output;

    /** @var SlugGenerator $slugGenerator */
    protected $slugGenerator;

    protected function configure()
    {
        $this
            ->setName('cycleways:slugs:refresh')
            ->setDescription('Refresh slugs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->slugGenerator = new SlugGenerator();

        $incidents = $this->doctrine->getRepository('CalderaBundle:Incident')->findAll();

        $progress = new ProgressBar($output, count($incidents));

        foreach ($incidents as $incident) {
            $this->process($incident);

            $progress->advance();
        }

        $this->manager->flush();
        $progress->finish();
    }

    protected function process(Incident $incident)
    {
        $slug = $this->slugGenerator->generateSlug($incident);

        $this->output->writeln(
            sprintf(
                'Incident <info>#%d</info> <comment>%s</comment> slug is: %s',
                $incident->getId(),
                $incident->getTitle(),
                $slug
            )
        );

        $this->manager->persist($incident);
    }
}