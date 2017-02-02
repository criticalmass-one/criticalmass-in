<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FacebookRideHandleCommand extends ContainerAwareCommand
{
    /**
     * @var Facebook $facebook
     */
    protected $facebook;

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:rides')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        $this->initFacebook();

        $cities = $this->doctrine->getRepository('CalderaBundle:City')->findCitiesWithFacebook();

        /**
         * @var City $city
         */
        foreach ($cities as $city) {
            $this->queryGraph($output, $city);
        }
    }

    protected function queryGraph(OutputInterface $output, City $city)
    {
        $month = new \DateTime('2016-03-04');
        $since = $this->getMonthStartDateTime($month)->format('U');
        $until = $this->getMonthEndDateTime($month)->format('U');

        $pageId = $this->getPageId($city);

        /**
         * @var FacebookResponse $response
         */
        $response = null;

        try {
            $response = $this->facebook->get('/' . $pageId . '/events?since=' . $since . '&until=' . $until);
        } catch (\Exception $e) {
            $output->writeln($pageId . ' ist ungültig');

            return null;
        }

        $eventEdge = null;

        try {
            /**
             * @var GraphEdge $eventEdge
             */
            $eventEdge = $response->getGraphEdge('GraphEvent');
        } catch (\Exception $e) {
            $output->writeln('Abfrage war ungültig');

            return null;
        }

        $rides = $this->doctrine->getRepository('CalderaBundle:Ride')->findByCityAndMonth($city, $month);

        if (count($rides) > 1) {
            $output->writeln('Zu viele Rides für diesen Monat');

            return null;
        }

        if (count($rides) == 0) {
            $output->writeln('Keine Rides für diesen Monat');

            return null;
        }

        $ride = array_pop($rides);

        if (count($eventEdge) > 1) {
            $output->writeln('Zu viele Events für diesen Monat');

            return null;
        }

        if (count($eventEdge) == 0) {
            $output->writeln('Keine Events für diesen Monat');

            return null;
        }

        /**
         * @var GraphEvent $event
         */
        foreach ($eventEdge as $event) {

        }

        $this->updateRide($ride, $event);

        $output->writeln('Aktualisierte Daten für diese Tour');

        $this->manager->persist($ride);
        $this->manager->flush();
    }

    protected function updateRide(Ride $ride, GraphEvent $event)
    {
        $archiveRide = clone $ride;
        $archiveRide->setArchiveUser(null);
        $archiveRide->setArchiveParent($ride);

        $ride->setDateTime($event->getStartTime());

        $ride->setTitle($event->getName());

        $ride->setDescription($event->getDescription());

        $place = $event->getPlace();

        if ($place) {
            $ride->setHasLocation(false);
            $ride->setLocation(null);
        } else {
            $ride->setHasLocation(true);
            $ride->setLocation($place);
        }
    }

    protected function initFacebook()
    {
        $this->facebook = new Facebook(
            [
                'app_id' => $this->getContainer()->getParameter('facebook.app_id'),
                'app_secret' => $this->getContainer()->getParameter('facebook.app_secret'),
                'default_graph_version' => 'v2.5',
                'default_access_token' => $this->getContainer()->getParameter('facebook.default_token')
            ]
        );
    }

    protected function getPageId(City $city)
    {
        $facebook = $city->getFacebook();

        if (strpos($facebook, 'https://www.facebook.com/') == 0) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $pageId = array_pop($parts);

            return $pageId;
        }

        return null;
    }


}