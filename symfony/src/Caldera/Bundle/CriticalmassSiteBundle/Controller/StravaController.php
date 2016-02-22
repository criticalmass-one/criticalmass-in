<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxExporter\GpxExporter;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Position;
use Polyline;
use Pest;
use Strava\API\Client;
use Symfony\Component\HttpFoundation\Request;
use Strava\API\OAuth;
use Strava\API\Service\REST;

class StravaController extends AbstractController
{
    public function authAction(Request $request)
    {
        try {
            $options = array(
                'clientId'     => $this->getParameter('strava.client_id'),
                'clientSecret' => $this->getParameter('strava.token'),
                'redirectUri'  => 'http://criticalmass.cm/app_dev.php/'
            );

            $oauth = new OAuth($options);

        } catch(Exception $e) {
            print $e->getMessage();
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Strava:auth.html.twig',
            [
                'authorizationUrl' => $oauth->getAuthorizationUrl()
            ]
        );
    }

    public function listridesAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $afterDateTime = new \DateTime($ride->getFormattedDate().' 00:00:00');
        $beforeDateTime = new \DateTime($ride->getFormattedDate().' 23:59:59');

        $adapter = new Pest('https://www.strava.com/api/v3');
        $service = new REST($this->getParameter('strava.token'), $adapter);

        $client = new Client($service);

        $activities = $client->getAthleteActivities($beforeDateTime->getTimestamp(), $afterDateTime->getTimestamp());

        print_r($activities);
        return $this->render(
            'CalderaCriticalmassSiteBundle:Strava:list.html.twig',
            [
                'activities' => $activities
            ]
        );
    }

    public function importAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $afterDateTime = new \DateTime($ride->getFormattedDate().' 00:00:00');
        $beforeDateTime = new \DateTime($ride->getFormattedDate().' 23:59:59');

        $adapter = new Pest('https://www.strava.com/api/v3');
        $service = new REST($this->getParameter('strava.token'), $adapter);

        $client = new Client($service);

        $activity = $client->getActivity(481324484, true);

        $startDateTime = new \DateTime($activity['start_date']);
        $startDateTime->setTimezone(new \DateTimeZone($activity['timezone']));
        $startTimestamp = $startDateTime->getTimestamp();

        $activityStream = $client->getStreamsActivity(481324484, 'time,latlng', 'high');

        $length = count($activityStream[0]['data']);

        $latLngList = $activityStream[0]['data'];
        $timeList = $activityStream[1]['data'];

        $positionArray = [];

        for ($i = 0; $i < $length; ++$i) {
            $position = new Position();
            $position->setLatitude($latLngList[$i][0]);
            $position->setLongitude($latLngList[$i][1]);
            $position->setTimestamp($startTimestamp + $timeList[$i]);
            $position->setCreationDateTime(new \DateTime());

            $positionArray[] = $position;
        }

        /**
         * @var GpxExporter $exporter
         */
        $exporter = $this->get('caldera.criticalmass.gps.gpxexporter');

        $exporter->setPositionArray($positionArray);

        $exporter->execute();

        echo $exporter->getGpxContent();

        return null;
        /*$this->render(
            'CalderaCriticalmassSiteBundle:Strava:import.html.twig',
            [
                'activity' => $activity
            ]
        );*/
    }
}
