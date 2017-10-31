<?php

namespace AppBundle\Command;

use AppBundle\Entity\Ride;
use AppBundle\Facebook\FacebookEventRideApi;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FacebookAutoSelectRideEventCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var ObjectManager $manager */
    protected $manager;

    /** @var Facebook $facebook */
    protected $facebook;

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:autoselectrideevent')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        /** @var FacebookEventRideApi $fera */
        $fera = $this->getContainer()->get('caldera.criticalmass.facebookapi.eventride');

        $rides = $this->doctrine->getRepository(Ride::class )->findFutureRides();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'DateTime', 'EventId', 'Status'])
        ;

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            if (!$ride->getFacebook()) {
                /** @var GraphEvent $event */
                $event = $fera->getEventForRide($ride);

                if ($event) {
                    $eventId = $event->getId();

                    $link = sprintf('https://www.facebook.com/events/%s', $eventId);

                    $ride->setFacebook($link);

                    $table
                        ->addRow([
                            $ride->getCity()->getCity(),
                            $ride->getDateTime()->format('Y-m-d H:i'),
                            $eventId,
                            'saved'
                        ])
                    ;
                } else {
                    $table
                        ->addRow([
                            $ride->getCity()->getCity(),
                            $ride->getDateTime()->format('Y-m-d H:i'),
                            'not found',
                            'not found'
                        ])
                    ;
                }
            } else {
                $table
                    ->addRow([
                        $ride->getCity()->getCity(),
                        $ride->getDateTime()->format('Y-m-d H:i'),
                        $this->getEventId($ride),
                        'already exists'
                    ])
                ;
            }
        }

        $table->render();
        $this->manager->flush();
    }

    protected function getEventId(Ride $ride): ?string
    {
        $facebook = $ride->getFacebook();

        if (strpos($facebook, 'https://www.facebook.com/') == 0) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $eventId = array_pop($parts);

            return $eventId;
        }

        return null;
    }

}