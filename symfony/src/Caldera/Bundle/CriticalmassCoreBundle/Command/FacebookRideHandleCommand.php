<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Facebook\FacebookRequest;
use \Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphNode;
use Facebook\GraphNodes\GraphPage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FacebookRideHandleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:rides')
            ->setDescription('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fb = new Facebook(
            [
                'app_id' => $this->getContainer()->getParameter('facebook.app_id'),
                'app_secret' => $this->getContainer()->getParameter('facebook.app_secret'),
                'default_graph_version' => 'v2.5',
                'default_access_token' => $this->getContainer()->getParameter('facebook.default_token')
            ]
        );

        /**
         * @var FacebookResponse $response
         */
        $response = $fb->get('/criticalmasshamburg/events');

        /**
         * @var GraphEdge $eventEdge
         */
        $eventEdge = $response->getGraphEdge('GraphEvent');

        /**
         * @var GraphEvent $event
         */
        foreach ($eventEdge as $event) {
            $output->writeln($event->getName());

            $output->writeln($event->getStartTime()->format('d.m.Y H:i'));
        }
    }
}