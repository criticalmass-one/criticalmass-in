<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\Track\TrackUploadedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Position;
use App\Entity\Ride;
use App\Entity\Track;
use App\Criticalmass\Gps\GpxExporter\GpxExporter;
use Strava\API\Client;
use Strava\API\OAuth;
use Strava\API\Service\REST;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class StravaController extends AbstractController
{
    protected function initOauthForRide(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): OAuth
    {
        $redirectUri = $request->getUriForPath($objectRouter->generate($ride, 'caldera_criticalmass_strava_token'));

        /* avoid double app_dev.php in uri */
        $redirectUri = str_replace('app_dev.php/app_dev.php/', 'app_dev.php/', $redirectUri);

        try {
            $oauthOptions = [
                'clientId' => $this->getParameter('strava.client_id'),
                'clientSecret' => $this->getParameter('strava.secret'),
                'redirectUri' => $redirectUri,
            ];

            return new OAuth($oauthOptions);

        } catch (\Exception $e) {
            print $e->getMessage();
        }
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function authAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): Response
    {
        $oauth = $this->initOauthForRide($request, $ride, $objectRouter);

        $authorizationOptions = [
            'state' => '',
            'approval_prompt' => 'force',
            'scope' => 'public',
        ];

        $authorizationUrl = $oauth->getAuthorizationUrl($authorizationOptions);

        return $this->render('Strava/auth.html.twig', [
            'authorizationUrl' => $authorizationUrl,
            'ride' => $ride,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function tokenAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): Response
    {
        $error = $request->get('error');

        if ($error) {
            return $this->redirect($objectRouter->generate($ride, 'caldera_criticalmass_strava_auth'));
        }

        $oauth = $this->initOauthForRide($request, $ride, $objectRouter);

        try {
            $token = $oauth->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            $session = $this->getSession();
            $session->set('strava_token', $token);

            return $this->redirect($objectRouter->generate($ride, 'caldera_criticalmass_strava_list'));
        } catch (\Exception $e) {
            return $this->redirect($objectRouter->generate($ride, 'caldera_criticalmass_strava_auth'));
        }
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function listridesAction(Ride $ride): Response
    {
        $afterDateTime = new \DateTime($ride->getDateTime()->format('Y-m-d') . ' 00:00:00');
        $beforeDateTime = new \DateTime($ride->getDateTime()->format('Y-m-d') . ' 23:59:59');

        $token = $this->getSession()->get('strava_token');

        $adapter = new \GuzzleHttp\Client(['base_uri' => 'https://www.strava.com/api/v3/']);
        $service = new REST($token, $adapter);
        $client = new Client($service);

        $activities = $client->getAthleteActivities($beforeDateTime->getTimestamp(), $afterDateTime->getTimestamp());

        return $this->render('Strava/list.html.twig', [
            'activities' => $activities,
            'ride' => $ride,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function importAction(Request $request, UserInterface $user, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, GpxExporter $exporter, Ride $ride): Response
    {
        $activityId = (int) $request->get('activityId');

        $uploadDestinationTrack = $this->getParameter('upload_destination.track');
        $token = $this->getSession()->get('strava_token');

        $adapter = new \GuzzleHttp\Client(['base_uri' => 'https://www.strava.com/api/v3/']);
        $service = new REST($token, $adapter);  // Define your user token here.
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

        $exporter->setPositionArray($positionArray);

        $exporter->execute();

        $filename = sprintf('%s.gpx', uniqid());

        $fp = fopen(sprintf('%s/%s', $uploadDestinationTrack, $filename), 'w');
        fwrite($fp, $exporter->getGpxContent());
        fclose($fp);

        $track = new Track();
        $track
            ->setSource(Track::TRACK_SOURCE_STRAVA)
            ->setStravaActivityId($activityId)
            ->setUser($user)
            ->setTrackFilename($filename)
            ->setUsername($user->getUsername())
            ->setRide($ride);

        $eventDispatcher->dispatch(TrackUploadedEvent::NAME, new TrackUploadedEvent($track));

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirect($objectRouter->generate($track));
    }
}
