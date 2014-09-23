<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Caldera\CriticalmassTimelineBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    public function writeAction(Request $request)
    {
        $post = new Post();
        $form = $this->createFormBuilder($post)
            ->add('message', 'text')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush();
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
