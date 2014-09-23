<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Caldera\CriticalmassTimelineBundle\Entity\Post;

class CityController extends Controller
{
    public function listAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array('enabled' => true), array('city' => 'ASC'));

        return $this->render('CalderaCriticalmassDesktopBundle:City:list.html.twig', array('cities' => $cities));
    }

    public function showAction(Request $request, $citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        if (!$city->getEnabled())
        {
            throw new NotFoundHttpException('Wir konnten keine Stadt unter der Bezeichnung "'.$citySlug.'" finden :(');
        }

        $post = new Post();
        $form = $this->createFormBuilder($post)
            ->add('message', 'text')
            ->add('LOS', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $post->setUser($this->getUser());
            $post->setCity($city);
            $em->persist($post);
            $em->flush();
        }

        $currentRide = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $city->getId()), array('dateTime' => 'DESC'));
        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findBy(array('city' => $city->getId()), array('dateTime' => 'DESC'));

        // shift the first ride from the array as the first one is the current and should not be displayed at the recent rides list
        array_shift($rides);

        return $this->render('CalderaCriticalmassDesktopBundle:City:show.html.twig', array('city' => $city, 'currentRide' => $currentRide, 'rides' => $rides, 'form' => $form->createView()));
    }
}
