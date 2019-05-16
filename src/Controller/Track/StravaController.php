<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Strava\Importer\TrackImporterInterface;
use App\Criticalmass\Util\DateTimeUtil;
use App\Event\Track\TrackUploadedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Ride;
use Strava\API\Client;
use Strava\API\OAuth;
use Strava\API\Service\REST;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StravaController extends AbstractController
{
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
    public function listridesAction(Ride $ride, SessionInterface $session): Response
    {
        $afterDateTime = DateTimeUtil::getDayStartDateTime($ride->getDateTime());
        $beforeDateTime = DateTimeUtil::getDayEndDateTime($ride->getDateTime());

        $token = $session->get('strava_token');

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
    public function importAction(Request $request, UserInterface $user, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Ride $ride, TrackImporterInterface $trackImporter): Response
    {
        $activityId = (int) $request->get('activityId');

        $track = $trackImporter
            ->setStravaActivityId($activityId)
            ->setRide($ride)
            ->setUser($user)
            ->importTrack();

        $eventDispatcher->dispatch(TrackUploadedEvent::NAME, new TrackUploadedEvent($track));

        return $this->redirect($objectRouter->generate($track));
    }

    protected function initOauthForRide(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): OAuth
    {
        $redirectUri = $request->getUriForPath($objectRouter->generate($ride, 'caldera_criticalmass_strava_token'));

        $oauthOptions = [
            'clientId' => $this->getParameter('strava.client_id'),
            'clientSecret' => $this->getParameter('strava.secret'),
            'redirectUri' => $redirectUri,
            'scopes' => ['view_private'],
        ];

        return new OAuth($oauthOptions);
    }
}
