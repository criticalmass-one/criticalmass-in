<?php

namespace Criticalmass\Bundle\UserBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ParticipationController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(Request $request, UserInterface $user): Response
    {
        $participationList = $this->getDoctrine()->getRepository('AppBundle:Participation')->findByUser($user, true);

        return $this->render(
            'UserBundle:Participation:list.html.twig',
            [
                'participationList' => $participationList
            ]
        );
    }

    /**
     * @Security("is_granted('cancel', participation)")
     * @ParamConverter("participation", class="AppBundle:Participation", options={"id": "participationId"})
     */
    public function cancelAction(RegistryInterface $registry, Participation $participation): Response
    {
        $participation
            ->setGoingNo(true)
            ->setGoingMaybe(false)
            ->setGoingYes(false);

        $registry->getManager()->flush();

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }

    /**
     * @Security("is_granted('delete', participation)")
     * @ParamConverter("participation", class="AppBundle:Participation", options={"id": "participationId"})
     */
    public function deleteAction(RegistryInterface $registry, Participation $participation): Response
    {
        $registry->getManager()->remove($participation);

        $registry->getManager()->flush();

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }
}
