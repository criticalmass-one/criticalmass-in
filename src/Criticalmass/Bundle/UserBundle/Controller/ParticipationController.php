<?php declare(strict_types=1);

namespace Criticalmass\Bundle\UserBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ParticipationController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(Registry $registry, UserInterface $user): Response
    {
        $participationList = $registry->getRepository(Participation::class)->findByUser($user, true);

        return $this->render('UserBundle:Participation:list.html.twig', [
            'participationList' => $participationList
        ]);
    }
}
