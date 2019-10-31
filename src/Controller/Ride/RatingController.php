<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Rating\Manager\RatingManagerInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RatingController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function rateAction(Request $request, Ride $ride, RatingManagerInterface $ratingManager, ObjectRouterInterface $objectRouter): Response
    {
        $stars = $request->query->getInt('stars', 0);

        $ratingManager->rateRide($ride, $stars);

        return $this->redirect($objectRouter->generate($ride));
    }
}
