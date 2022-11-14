<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Strava\Importer\TrackImporterInterface;
use App\Criticalmass\Strava\Token\StravaTokenStorage;
use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\Ride;
use App\Event\Track\TrackUploadedEvent;
use Iamstuartwilson\StravaApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Strava\API\OAuth;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StravaController extends AbstractController
{
    public function __construct(private readonly string $stravaClientId, private readonly string $stravaSecret)
    {
    }
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function authAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): Response
    {
        $redirect = $request->getUriForPath($objectRouter->generate($ride, 'caldera_criticalmass_strava_token'));

        $api = $this->createApi();

        $authenticationUrl = $api->authenticationUrl(
            $redirect,
            'auto',
            'activity:read_all',
            null
        );

        return $this->render('Strava/auth.html.twig', [
            'authorizationUrl' => $authenticationUrl,
            'ride' => $ride,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function tokenAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter, SessionInterface $session): Response
    {
        $api = $this->createApi();

        $code = $request->query->get('code');
        $result = $api->tokenExchange($code);

        try {
            $token = StravaTokenStorage::createFromStravaResponse($result);

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

        $api = $this->createApi();

        StravaTokenStorage::setAccessToken($api, $session->get('strava_token'));

        $activities = $api->get(
            '/athlete/activities', [
                'before' => $beforeDateTime->getTimestamp(),
                'after' => $afterDateTime->getTimestamp(),
                'page' => 1,
                'per_page' => 50,
            ]
        );

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

        $eventDispatcher->dispatch(new TrackUploadedEvent($track), TrackUploadedEvent::NAME);

        return $this->redirect($objectRouter->generate($track));
    }

    protected function initOauthForRide(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): OAuth
    {
        $redirectUri = $request->getUriForPath($objectRouter->generate($ride, 'caldera_criticalmass_strava_token'));

        $oauthOptions = [
            'clientId' => $this->stravaClientId,
            'clientSecret' => $this->stravaSecret,
            'redirectUri' => $redirectUri,
            'scope' => 'read',
        ];

        return new OAuth($oauthOptions);
    }

    protected function createApi(): StravaApi
    {
        $api = new StravaApi($this->stravaClientId, $this->stravaSecret);

        return $api;
    }
}
