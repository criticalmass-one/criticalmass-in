<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Facebook\FacebookEventRideApi;
use Caldera\Bundle\CriticalmassCoreBundle\Facebook\FacebookPageApi;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Facebook\FacebookRequest;
use \Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphNode;
use Facebook\GraphNodes\GraphPage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FacebookAutoSelectRideEventCommand extends ContainerAwareCommand
{
    /**
     * @var Facebook $facebook
     */
    protected $facebook;

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:autoselectrideevent')
            ->setDescription('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        /**
         * @var FacebookEventRideApi $fera
         */
        $fera = $this->getContainer()->get('caldera.criticalmass.facebookapi.eventride');

        $rides = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Ride')->findRidesInInterval();

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            $output->writeln('Looking up current ride for: '.$ride->getCity()->getCity());

            if (!$ride->getFacebook()) {
                $output->writeln($ride->getFancyTitle().' has no facebook details.');

                /**
                 * @var GraphEvent $event
                 */
                $event = $fera->getEventForRide($ride);

                if ($event) {
                    $eventId = $event->getId();

                    $link = 'https://www.facebook.com/events/'.$eventId;

                    $ride->setFacebook($link);

                    $this->manager->persist($ride);

                    $output->writeln('Saved '.$eventId.' as ride id.');
                } else {
                    $output->writeln('Could not auto-detect facebook event for this ride.');
                }
            } else {
                $output->writeln($ride->getFancyTitle().' already has facebook details.');
            }
        }

        $this->manager->flush();
    }

    protected function getEventId(Ride $ride)
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