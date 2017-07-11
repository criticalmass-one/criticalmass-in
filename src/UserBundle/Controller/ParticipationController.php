<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
}
