<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Participation\Manager\ParticipationManagerInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ParticipationController extends AbstractController
{
    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    #[IsGranted('ROLE_USER')]
    public function rideparticipationAction(ParticipationManagerInterface $participationManager, ObjectRouterInterface $objectRouter, Ride $ride, string $status): Response
    {
        $participationManager->participate($ride, $status);

        return $this->redirect($objectRouter->generate($ride));
    }
}
