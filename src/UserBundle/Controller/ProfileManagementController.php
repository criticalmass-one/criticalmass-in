<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\Form\Type\UsernameEmailType;

class ProfileManagementController extends Controller
{
    public function manageAction(Request $request, UserInterface $user): Response
    {
        $participationCounter = $this->getDoctrine()->getRepository('AppBundle:Participation')->countByUser($user);
        $trackCounter = $this->getDoctrine()->getRepository('AppBundle:Track')->countByUser($user);
        $photoCounter = $this->getDoctrine()->getRepository('AppBundle:Photo')->countByUser($user);

        return $this->render(
            'UserBundle:ProfileManagement:manage.html.twig',
            [
                'participationCounter' => $participationCounter,
                'trackCounter' => $trackCounter,
                'photoCounter' => $photoCounter,
            ]
        );
    }

    public function editAction(Request $request, UserInterface $user): Response
    {
        $userForm = $this->createForm(
            UsernameEmailType::class,
            $user,
            [
                'action' => $this->generateUrl(
                    'criticalmass_user_usermanagement_edit'
                )
            ]
        );

        if ($request->isMethod(Request::METHOD_POST)) {
            $userForm->handleRequest($request);

            if ($userForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirectToRoute('criticalmass_user_usermanagement');
        }

        return $this->render(
            'UserBundle:ProfileManagement:edit.html.twig',
            [
                'userForm' => $userForm->createView()
            ]
        );
    }
}
