<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\FacebookCityProperties;
use Criticalmass\Bundle\AppBundle\Entity\Region;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Bundle\AppBundle\Form\Type\SocialNetworkProfileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkController extends AbstractController
{
    public function addAction(Request $request, UserInterface $user): Response
    {
        $socialNetworkProfile = new SocialNetworkProfile();

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

    }

    protected function addGetAction(Request $request, UserInterface $user, FormInterface $form): Response
    {
        return $this->render('AppBundle:SocialNetwork:edit.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }
}
