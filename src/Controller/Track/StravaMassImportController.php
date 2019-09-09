<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\MassTrackImport\MassTrackImporterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Strava\Importer\TrackImporterInterface;
use App\Entity\Ride;
use App\Event\Track\TrackUploadedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Strava\API\OAuth;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StravaMassImportController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function authAction(Request $request, RouterInterface $router): Response
    {
        $oauth = $this->initOauthForRide($request, $router);

        $authorizationOptions = [
            'state' => '',
            'approval_prompt' => 'force',
            'scope' => 'public',
        ];

        $authorizationUrl = $oauth->getAuthorizationUrl($authorizationOptions);

        return $this->render('TrackMassImport/auth.html.twig', [
            'authorizationUrl' => $authorizationUrl,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function tokenAction(Request $request, SessionInterface $session, RouterInterface $router): Response
    {
        $error = $request->get('error');

        if ($error) {
            return $this->redirect($router->generate('caldera_criticalmass_trackmassimport_auth'));
        }

        $oauth = $this->initOauthForRide($request, $router);

        try {
            $token = $oauth->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            $session->set('strava_token', $token);

            return $this->redirect($router->generate('caldera_criticalmass_trackmassimport_list'));
        } catch (\Exception $e) {
            return $this->redirect($router->generate('caldera_criticalmass_trackmassimport_auth'));
        }
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listridesAction(MassTrackImporterInterface $massTrackImporter, TrackDeciderInterface $trackDecider): Response
    {
        $list = $massTrackImporter
            ->setStartDateTime(new \DateTime('2019-08-01 00:00:00'))
            ->setEndDateTime(new \DateTime())
            ->execute();

        return $this->render('TrackMassImport/list.html.twig', [
            'list' => $list,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function importAction(Request $request, UserInterface $user, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Ride $ride, TrackImporterInterface $trackImporter): Response
    {
        $activityId = (int)$request->get('activityId');

        $track = $trackImporter
            ->setStravaActivityId($activityId)
            ->setRide($ride)
            ->setUser($user)
            ->importTrack();

        $eventDispatcher->dispatch(TrackUploadedEvent::NAME, new TrackUploadedEvent($track));

        return $this->redirect($objectRouter->generate($track));
    }

    protected function initOauthForRide(Request $request, RouterInterface $router): OAuth
    {
        $redirectUri = $request->getUriForPath($router->generate('caldera_criticalmass_trackmassimport_token'));

        $oauthOptions = [
            'clientId' => $this->getParameter('strava.client_id'),
            'clientSecret' => $this->getParameter('strava.secret'),
            'redirectUri' => $redirectUri,
            'scopes' => ['view_private'],
        ];

        return new OAuth($oauthOptions);
    }
}
