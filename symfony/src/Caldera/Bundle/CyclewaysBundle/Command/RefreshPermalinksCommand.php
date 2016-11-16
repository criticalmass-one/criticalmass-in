<?php

namespace Caldera\Bundle\CyclewaysBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CyclewaysBundle\PermalinkManager\SqibePermalinkManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class RefreshPermalinksCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var OutputInterface $output */
    protected $output;

    /** @var SqibePermalinkManager $permalinkManager */
    protected $permalinkManager;

    protected function configure()
    {
        $this
            ->setName('cycleways:permalink:refresh')
            ->setDescription('Refresh permalinks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->permalinkManager = $this->getContainer()->get('caldera.cycleways.permalink_manager.sqibe');

        /** @var RequestContext $context */
        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost($this->getContainer()->getParameter('domain.cycleways'));
        $context->setScheme('https');
        $context->setBaseUrl('');

        $incidents = $this->doctrine->getRepository('CalderaBundle:Incident')->findAll();

        foreach ($incidents as $incident) {
            $this->process($incident);
        }

        $this->manager->flush();
    }

    protected function process(Incident $incident)
    {
        $this->output->writeln('');

        if (!$incident->getSlug()) {
            return;
        }

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

            $this->createPermalink($incident);

            return;
        }


        $this->output->writeln(
            sprintf(
                'Current permalink is: %s',
                $incident->getPermalink()
            )
        );

        $longUrl = $this->permalinkManager->getUrl($incident);


        if (!$longUrl) {
            $this->output->writeln(
                'Long url could not be found'
            );

            $this->createPermalink($incident);

            return;
        }

        $this->output->writeln(
            sprintf(
                'Current url is: %s',
                $longUrl
            )
        );

        $generatedUrl = $this->generateUrl($incident);

        if ($generatedUrl != $longUrl) {
            $this->output->writeln(
                sprintf(
                    'Url mismatch. Generated: %s',
                    $generatedUrl
                )
            );

            $this->updatePermalink($incident);
        }
    }

    protected function generateUrl(Incident $incident): string
    {
        $url = $this->getContainer()->get('router')->generate(
            'caldera_cycleways_incident_show',
            [
                'slug' => $incident->getSlug()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $url;
    }


    protected function createPermalink(Incident $incident)
    {
        $permalink = $this->permalinkManager->createPermalink($incident);

        $this->output->writeln(sprintf(
            'Created permalink: %s',
            $permalink
        ));

        $this->manager->persist($incident);
    }

    protected function updatePermalink(Incident $incident)
    {
        $success = $this->permalinkManager->updatePermalink($incident);

        if ($success) {
            $this->output->writeln(
                'Updated permalink'
            );

            $this->manager->persist($incident);
        } else {
            $this->output->writeln(
                'Could not update permalink'
            );
        }
    }
}