<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\DistanceCalculator\TrackDistanceCalculator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxExporter\GpxExporter;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Position;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Exception;
use Polyline;
use Pest;
use Strava\API\Client;
use Symfony\Component\HttpFoundation\Request;
use Strava\API\OAuth;
use Strava\API\Service\REST;

class StravaController extends AbstractController
{
    public function authAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $redirectUri = 'http://www.criticalmass.cm'.$this->generateUrl(
            'caldera_criticalmass_strava_list',
            [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate(),
                true
            ]
        );

        try {
            $oauthOptions = [
                'clientId'     => $this->getParameter('strava.client_id'),
                'clientSecret' => $this->getParameter('strava.secret'),
                'redirectUri'  => $redirectUri,
                'scopes' => ['view_private']
            ];

            $oauth = new OAuth($oauthOptions);

        } catch(Exception $e) {
            print $e->getMessage();
        }

        $authorizationOptions = [
            'state' => '',
            'approval_prompt' => 'force'
        ];

        return $this->render(
            'CalderaCriticalmassSiteBundle:Strava:auth.html.twig',
            [
                'authorizationUrl' => $oauth->getAuthorizationUrl($authorizationOptions)
            ]
        );
    }

    public function listridesAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $afterDateTime = new \DateTime($ride->getFormattedDate().' 00:00:00');
        $beforeDateTime = new \DateTime($ride->getFormattedDate().' 23:59:59');

        $redirectUri = 'http://www.criticalmass.cm'.$this->generateUrl(
                'caldera_criticalmass_strava_list',
                [
                    'citySlug' => $ride->getCity()->getMainSlugString(),
                    'rideDate' => $ride->getFormattedDate(),
                    true
                ]
            );

        $oauthOptions = [
            'clientId'     => $this->getParameter('strava.client_id'),
            'clientSecret' => $this->getParameter('strava.secret'),
            'redirectUri'  => $redirectUri
        ];

        $oauth = new OAuth($oauthOptions);

        $token = $oauth->getAccessToken(
            'authorization_code',
            [
                'code' => $request->get('code')
            ]
        );

        $adapter = new Pest('https://www.strava.com/api/v3');
        $service = new REST($token, $adapter);

        $client = new Client($service);

        $activities = $client->getAthleteActivities($beforeDateTime->getTimestamp(), $afterDateTime->getTimestamp());

        return $this->render(
            'CalderaCriticalmassSiteBundle:Strava:list.html.twig',
            [
                'activities' => $activities,
                'ride' => $ride
            ]
        );
    }

    public function importAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        $activityId = $request->get('activityId');

        $adapter = new Pest('https://www.strava.com/api/v3');
        $service = new REST($this->getParameter('strava.token'), $adapter);

        $client = new Client($service);

        /* Catch the activity to retrieve the start dateTime */
        $activity = $client->getActivity($activityId, true);

        $startDateTime = new \DateTime($activity['start_date']);
        $startDateTime->setTimezone(new \DateTimeZone($activity['timezone']));
        $startTimestamp = $startDateTime->getTimestamp();

        /* Now fetch all the gpx data we need */
        $activityStream = $client->getStreamsActivity($activityId, 'time,latlng,altitude', 'high');

        $length = count($activityStream[0]['data']);

        $latLngList = $activityStream[0]['data'];
        $timeList = $activityStream[1]['data'];
        $altitudeList = $activityStream[2]['data'];

        $positionArray = [];

        for ($i = 0; $i < $length; ++$i) {
            $altitude = round($i > 0 ? $altitudeList[$i] - $altitudeList[$i - 1] : $altitudeList[$i], 2);

            $position = new Position();

            $position->setLatitude($latLngList[$i][0]);
            $position->setLongitude($latLngList[$i][1]);
            $position->setAltitude($altitude);
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

        $track = new Track();
        $track->setUser($this->getUser());
        $track->setRide($ride);
        $track->setTrackFilename(uniqid().'.gpx');

        $filename = uniqid() . '.gpx';

        $fp = fopen('../web/tracks/' . $filename, 'w');
        fwrite($fp, $exporter->getGpxContent());
        fclose($fp);

        $track = new Track();
        $track->setUser($this->getUser());
        $track->setTrackFilename($filename);
        $track->setUsername($this->getUser()->getUsername());
        $track->setRide($ride);

        $this->loadTrackProperties($track);
        $this->generateSimpleLatLngList($track);

        $this->addRideEstimate($track, $ride);

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute(
            'caldera_criticalmass_track_view',
            [
                'trackId' => $track->getId()
            ]
        );
    }

    protected function saveLatLngList(Track $track)
    {
        /**
         * @var RangeLatLngListGenerator $llag
         */
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.range');
        $llag->loadTrack($track);
        $llag->execute();
        $track->setLatLngList($llag->getList());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    protected function addRideEstimate(Track $track, Ride $ride)
    {
        /**
         * @var RideEstimateService $estimateService
         */
        $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate.track');
        $estimateService->addEstimate($track);
        $estimateService->calculateEstimates($ride);
    }

    protected function loadTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $gr = $this->get('caldera.criticalmass.gps.trackreader');
        $gr->loadTrack($track);

        $track->setPoints($gr->countPoints());

        $track->setStartPoint(0);
        $track->setEndPoint($gr->countPoints() - 1);

        $track->setStartDateTime($gr->getStartDateTime());
        $track->setEndDateTime($gr->getEndDateTime());
        $track->setMd5Hash($gr->getMd5Hash());

        /**
         * @var TrackDistanceCalculator $tdc
         */
        $tdc = $this->get('caldera.criticalmass.gps.distancecalculator.track');
        $tdc->loadTrack($track);

        $track->setDistance($tdc->calculate());
    }

    protected function generateSimpleLatLngList(Track $track)
    {
        /**
         * @var SimpleLatLngListGenerator $generator
         */
        $generator = $this->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $list = $generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $track->setLatLngList($list);
    }
}
