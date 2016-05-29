<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\UserPhoneNumberType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhoneNumberController extends AbstractController
{
    public function editAction(Request $request)
    {
        $form = $this->createForm(
            new UserPhoneNumberType(),
            $this->getUser(),
            [
                'action' => $this->generateUrl('caldera_criticalmass_phonenumber_edit')
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $form);
        } else {
            return $this->editGetAction($request, $form);
        }
    }

    protected function editGetAction(Request $request, Form $form)
    {
        return $this->render('CalderaCriticalmassSiteBundle:Profile:editmobilephonenumber.html.twig',
            [
                'mobilePhoneNumberForm' => $form->createView()
            ]
        );
    }

    protected function editPostAction(Request $request, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();
        }

        return $this->editGetAction($request, $form);
    }
}
