<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Participation\Manager\ParticipationManagerInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class ParticipationController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function rideparticipationAction(ParticipationManagerInterface $participationManager, ObjectRouterInterface $objectRouter, Ride $ride, string $status): Response
    {
        $participationManager->participate($ride, $status);

        return $this->redirect($objectRouter->generate($ride));
    }
}
