<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\UserPhoneNumberType;
use Caldera\Bundle\CalderaBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhoneNumberController extends AbstractController
{
    public function editAction(Request $request)
    {
        $numberForm = $this->createForm(
            new UserPhoneNumberType(),
            $this->getUser(),
            [
                'action' => $this->generateUrl('caldera_criticalmass_phonenumber_edit')
            ]
        );

        $verificationForm = $this->createFormBuilder()
            ->add('verificationNumber', TextType::class)
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $numberForm, $verificationForm);
        } else {
            return $this->editGetAction($request, $numberForm, $verificationForm);
        }
    }

    protected function editGetAction(Request $request, Form $numberForm, Form $verificationForm)
    {
        return $this->render('CalderaCriticalmassSiteBundle:Profile:edit.html.twig',
            [
                'phoneNumberForm' => $numberForm->createView(),
                'verificationForm' => $verificationForm->createView()
            ]
        );
    }

    protected function editPostAction(Request $request, Form $numberForm, Form $verificationForm)
    {
        $numberForm->handleRequest($request);

        if ($numberForm->isValid()) {
            /**
             * @var User $user
             */
            $user = $this->getUser();

            $user->setPhoneNumberVerified(false);
            $user->setPhoneNumberVerificationToken(mt_rand(100000, 999999));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->editGetAction($request, $numberForm, $verificationForm);
    }
}
