<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Rating\Manager\RatingManagerInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RatingController extends AbstractController
{
    #[Route(path: '/{citySlug}/{rideIdentifier}/rate', name: 'caldera_criticalmass_ride_rate')]
    #[IsGranted('ROLE_USER')]
    public function rateAction(Request $request, Ride $ride, RatingManagerInterface $ratingManager, ObjectRouterInterface $objectRouter): Response
    {
        $stars = $request->query->getInt('stars', 0);

        if ($stars < 1 || $stars > 5) {
            throw $this->createNotFoundException('Rating must be between 1 and 5 stars');
        }

        $ratingManager->rateRide($ride, $stars);

        return $this->redirect($objectRouter->generate($ride));
    }
}
