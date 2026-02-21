<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Participation\Manager\ParticipationManagerInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ParticipationController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/participation/{status}',
        name: 'caldera_criticalmass_participation_ride',
        methods: ['POST'],
        priority: 160
    )]
    public function rideparticipationAction(
        Request $request,
        ParticipationManagerInterface $participationManager,
        ObjectRouterInterface $objectRouter,
        Ride $ride,
        string $status
    ): Response {
        if (!$this->isCsrfTokenValid('participation_' . $ride->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $participationManager->participate($ride, $status);

        return $this->redirect($objectRouter->generate($ride));
    }
}
