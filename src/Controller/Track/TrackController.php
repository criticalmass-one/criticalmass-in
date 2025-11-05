<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Strava\Importer\TrackImporterInterface;
use App\Criticalmass\Strava\Token\StravaTokenStorage;
use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\Ride;
use App\Event\Track\TrackUploadedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Iamstuartwilson\StravaApi;
use Strava\API\OAuth;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
// ↓ neu
use Symfony\Component\Routing\Attribute\Route;

class StravaController extends AbstractController
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly string $stravaClientId,
        private readonly string $stravaSecret
    )
    {
        parent::__construct($managerRegistry);
    }

    #[IsGranted('ROLE_USER')]
    public function authAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): Response { /* unverändert */ }

    #[IsGranted('ROLE_USER')]
    public function tokenAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter, SessionInterface $session): Response { /* unverändert */ }

    #[IsGranted('ROLE_USER')]
    public function listridesAction(Ride $ride, SessionInterface $session): Response { /* unverändert */ }

    #[IsGranted('ROLE_USER')]
    public function importAction(Request $request, UserInterface $user, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Ride $ride, TrackImporterInterface $trackImporter): Response { /* unverändert */ }

    protected function initOauthForRide(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): OAuth { /* unverändert */ }

    protected function createApi(): StravaApi { /* unverändert */ }
}
