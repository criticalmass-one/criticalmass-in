<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\TrackHandlingTrait;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxExporter\GpxExporter;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Position;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Polyline;
use Pest;
use Strava\API\Client;
use Symfony\Component\HttpFoundation\Request;
use Strava\API\OAuth as OAuth;
use Strava\API\Service\REST;
use Symfony\Component\HttpFoundation\Session\Session;

class StravaController extends AbstractController
{
    use TrackHandlingTrait;

    protected function initOauthForRide(Request $request, Ride $ride)
    {
        $redirectUri = $request->getUriForPath($this->generateUrl(
            'caldera_criticalmass_strava_token',
            [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate()
            ]
        ));

        /* avoid double app_dev.php in uri */
        $redirectUri = str_replace('app_dev.php/app_dev.php/', 'app_dev.php/', $redirectUri);

        try {
            $oauthOptions = [
                'clientId'     => $this->getParameter('strava.client_id'),
                'clientSecret' => $this->getParameter('strava.secret'),
                'redirectUri'  => $redirectUri,
                'scopes' => ['view_private']
            ];

            return new OAuth($oauthOptions);

        } catch(\Exception $e) {
            print $e->getMessage();
        }
    }

    public function authAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $oauth = $this->initOauthForRide($request, $ride);

        $authorizationOptions = [
            'state' => '',
            'approval_prompt' => 'force'
        ];

        $authorizationUrl = $oauth->getAuthorizationUrl($authorizationOptions);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Strava:auth.html.twig',
            [
                'authorizationUrl' => $authorizationUrl,
                'ride' => $ride
            ]
        );
    }

    public function tokenAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $error = $request->get('error');

        if ($error) {
            return $this->redirectToRoute(
                'caldera_criticalmass_strava_auth',
                [
                    'citySlug' => $citySlug,
                    'rideDate' => $rideDate
                ]
            );
        }

        $oauth = $this->initOauthForRide($request, $ride);

        try {
            $token = $oauth->getAccessToken(
                'authorization_code',
                [
                    'code' => $request->get('code')
                ]
            );

            $session = $this->getSession();
            $session->set('strava_token', $token);

            return $this->redirectToRoute(
                'caldera_criticalmass_strava_list',
                [
                    'citySlug' => $citySlug,
                    'rideDate' => $rideDate
                ]
            );
        } catch (\Exception $e) {
            return $this->redirectToRoute(
                'caldera_criticalmass_strava_auth',
                [
                    'citySlug' => $citySlug,
                    'rideDate' => $rideDate
                ]
            );
        }
    }

    public function listridesAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $afterDateTime = new \DateTime($ride->getFormattedDate().' 00:00:00');
        $beforeDateTime = new \DateTime($ride->getFormattedDate().' 23:59:59');

        $token = $this->getSession()->get('strava_token');

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

        $token = $this->getSession()->get('strava_token');

        $adapter = new Pest('https://www.strava.com/api/v3');
        $service = new REST($token, $adapter);

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
        $this->generatePolyline($track);

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
}
