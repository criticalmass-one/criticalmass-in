<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Rating;
use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class RatingController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function rateAction(Request $request, Ride $ride, UserInterface $user, RegistryInterface $registry, ObjectRouterInterface $objectRouter): Response
    {
        $stars = $request->query->getInt('stars', 0);

        $rating = new Rating();
        $rating
            ->setUser($user)
            ->setRide($ride)
            ->setRating($stars);

        $em = $registry->getManager();
        $em->persist($rating);
        $em->flush();

        return $this->redirect($objectRouter->generate($ride));
    }
}
