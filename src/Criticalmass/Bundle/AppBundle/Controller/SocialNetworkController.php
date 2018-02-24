<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Bundle\AppBundle\Entity\Subride;
use Criticalmass\Bundle\AppBundle\Form\Type\SocialNetworkProfileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkController extends AbstractController
{
    public function addAction(Request $request, UserInterface $user, City $city = null, Ride $ride = null, Subride $subride = null): Response
    {
        $socialNetworkProfile = new SocialNetworkProfile();

        $socialNetworkProfile
            ->setUser($user)
            ->setCity($city)
            ->setRide($ride)
            ->setSubride($subride)
        ;

        $form = $this->createForm(
            SocialNetworkProfileType::class,
            $socialNetworkProfile
        );

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->addPostAction($request, $user, $form);
        } else {
            return $this->addGetAction($request, $user, $form);
        }
    }

    protected function addPostAction(Request $request, UserInterface $user, FormInterface $form): Response
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $socialNetworkProfile = $form->getData();

            $this->getDoctrine()->getManager()->persist($socialNetworkProfile);
        }

        return new Response('asdf');
    }

    protected function addGetAction(Request $request, UserInterface $user, FormInterface $form): Response
    {
        return $this->render('AppBundle:SocialNetwork:edit.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }
}
