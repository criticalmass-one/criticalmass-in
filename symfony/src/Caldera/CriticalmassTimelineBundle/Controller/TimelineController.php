<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Caldera\CriticalmassTimelineBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimelineController extends Controller
{
    public function listAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->findBy(array('enabled' => true), array('dateTime' => 'DESC'));

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
            $em->persist($post);
            $em->flush();
        }

        return $this->render('CalderaCriticalmassTimelineBundle:Timeline:list.html.twig', array('posts' => $posts, 'form' => $form->createView()));
    }
}
