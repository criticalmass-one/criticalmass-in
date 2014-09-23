<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Caldera\CriticalmassTimelineBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    public function writeAction(Request $request, $cityId = null, $rideId = null)
    {
        $post = new Post();
        $formBuilder = $this->createFormBuilder($post)

            ->add('message', 'textarea');

        if ($cityId)
        {
            $formBuilder->setAction($this->generateUrl('caldera_criticalmass_timeline_post_write_city', array('cityId' => $cityId)));
            $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->find($cityId);
            $post->setCity($city);
        }

        if ($rideId)
        {
            $formBuilder->setAction($this->generateUrl('caldera_criticalmass_timeline_post_write_ride', array('rideId' => $rideId)));
            $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);
            $post->setRide($ride);
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush();

            return new RedirectResponse($this->container->get('request')->headers->get('referer'));
        }

        return $this->render('CalderaCriticalmassTimelineBundle:Post:write.html.twig', array('form' => $form->createView()));
    }


    public function listAction(Request $request, $cityId = null, $rideId = null)
    {
        $criteria = array('enabled' => true);

        if ($cityId)
        {
            $criteria['city'] = $cityId;
        }

        if ($rideId)
        {
            $criteria['ride'] = $rideId;
        }

        $posts = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->findBy($criteria, array('dateTime' => 'DESC'));

        return $this->render('CalderaCriticalmassTimelineBundle:Post:list.html.twig', array('posts' => $posts));
    }
}
