<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PhotoCoordType;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\UserMobilePhoneNumberType;
use Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader\DateTimeExifReader;
use Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoGps\PhotoGps;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\PhotoView;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends AbstractController
{
    public function editmobilephonenumberAction(Request $request)
    {
        $form = $this->createForm(
            new UserMobilePhoneNumberType(),
            $this->getUser(),
            [
                'action' => $this->generateUrl('caldera_criticalmass_profile_editmobilephonenumber')
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->editmobilephonenumberPostAction($request, $form);
        } else {
            return $this->editmobilephonenumberGetAction($request, $form);
        }
    }

    protected function editmobilephonenumberGetAction(Request $request, Form $form)
    {
        return $this->render('CalderaCriticalmassSiteBundle:Profile:editmobilephonenumber.html.twig',
            [
                'mobilePhoneNumberForm' => $form->createView()
            ]
        );
    }

    protected function editmobilephonenumberPostAction(Request $request, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();
        }

        return $this->editmobilephonenumberGetAction($request, $form);
    }
}
