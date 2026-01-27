<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\MassTrackImport\MassTrackImporterInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Strava\Importer\TrackImporterInterface;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use App\Event\Track\TrackUploadedEvent;
use Carbon\Carbon;
use Strava\API\OAuth;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StravaMassImportController extends AbstractController
{
    public function __construct(private readonly string $stravaClientId, private readonly string $stravaSecret)
    {

    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackimport/stravaauth', name: 'caldera_criticalmass_trackmassimport_auth', priority: 310)]
    public function authAction(Request $request, RouterInterface $router): Response
    {
        $oauth = $this->initOauth($request, $router);

        $authorizationOptions = [
            'state' => '',
            'approval_prompt' => 'force',
            'scope' => 'read',
        ];

        $authorizationUrl = $oauth->getAuthorizationUrl($authorizationOptions);

        return $this->render('TrackMassImport/auth.html.twig', [
            'authorizationUrl' => $authorizationUrl,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackimport/stravatoken', name: 'caldera_criticalmass_trackmassimport_token', priority: 310)]
    public function tokenAction(Request $request, SessionInterface $session, RouterInterface $router): Response
    {
        $error = $request->get('error');
        $year = $request->query->getInt('year', (Carbon::now())->format('Y'));

        if ($error) {
            return $this->redirect($router->generate('caldera_criticalmass_trackmassimport_auth'));
        }

        $oauth = $this->initOauth($request, $router);

        try {
            $token = $oauth->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            $session->set('strava_token', $token);

            return $this->redirect($router->generate('caldera_criticalmass_trackmassimport_massimport', [
                'year' => $year,
            ]));
        } catch (\Exception $e) {
            return $this->redirect($router->generate('caldera_criticalmass_trackmassimport_auth'));
        }
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackimport/stravamassimport', name: 'caldera_criticalmass_trackmassimport_massimport', priority: 310)]
    public function massImportAction(Request $request, MassTrackImporterInterface $massTrackImporter): Response
    {
        $year = $request->query->getInt('year', (Carbon::now())->format('Y'));

        $carbon = Carbon::createFromDate($year);
        $startCarbon = $carbon->startOfYear();
        $endCarbon = $carbon->endOfYear();

        if ($endCarbon->greaterThanOrEqualTo(Carbon::now())) {
            $endCarbon = Carbon::now();
        }

        $massTrackImporter
            ->setStartDateTime($startCarbon)
            ->setEndDateTime($endCarbon)
            ->execute();

        return $this->redirectToRoute('caldera_criticalmass_trackmassimport_list');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackimport/stravalist', name: 'caldera_criticalmass_trackmassimport_list', priority: 310)]
    public function listridesAction(ManagerRegistry $registry, ?UserInterface $user = null): Response
    {
        $list = $registry->getRepository(TrackImportCandidate::class)->findCandidatesForUser($user);

        return $this->render('TrackMassImport/list.html.twig', [
            'list' => $list,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackimport/reject', name: 'caldera_criticalmass_trackmassimport_reject', priority: 310)]
    public function rejectAction(Request $request, UserInterface $user, ObjectRouterInterface $objectRouter, ManagerRegistry $registry): Response
    {
        $activityId = (int)$request->get('activityId');

        /** @var TrackImportCandidate $proposal */
        $proposal = $registry->getRepository(TrackImportCandidate::class)->findOneByActivityId($activityId);

        if ($proposal->getUser() !== $user) {
            throw new AccessDeniedHttpException();
        }

        $proposal->setRejected(true);

        $registry->getManager()->flush();

        return $this->redirectToRoute('caldera_criticalmass_trackmassimport_list');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackimport/stravaimport', name: 'caldera_criticalmass_trackmassimport_import', priority: 310)]
    public function importAction(Request $request, UserInterface $user, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Ride $ride, TrackImporterInterface $trackImporter): Response
    {
        $activityId = (int)$request->get('activityId');

        $track = $trackImporter
            ->setStravaActivityId($activityId)
            ->setRide($ride)
            ->setUser($user)
            ->importTrack();

        $eventDispatcher->dispatch(new TrackUploadedEvent($track), TrackUploadedEvent::NAME);

        return $this->redirect($objectRouter->generate($track));
    }

    protected function initOauth(Request $request, RouterInterface $router): OAuth
    {
        $year = $request->query->getInt('year', (int) (Carbon::now())->format('Y'));

        $redirectUri = $request->getUriForPath($router->generate('caldera_criticalmass_trackmassimport_token', [
            'year' => $year,
        ]));

        $oauthOptions = [
            'clientId' => $this->stravaClientId,
            'clientSecret' => $this->stravaSecret,
            'redirectUri' => $redirectUri,
            'scope' => 'read',
        ];

        return new OAuth($oauthOptions);
    }
}
