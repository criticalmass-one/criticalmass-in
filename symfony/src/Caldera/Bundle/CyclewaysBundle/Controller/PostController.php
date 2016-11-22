<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CalderaBundle\Entity\Post;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PostType;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function writeAction(Request $request, Incident $incident): Response
    {
        if (!$this->isLoggedIn()) {
            $this->createAccessDeniedException();
        }

        $post = new Post();

        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'action' => $this->generateUrl(
                    'caldera_cycleways_incident_comment_add',
                    [
                        'slug' => $incident->getSlug()
                    ]
                )
            ]
        );

        $post->setIncident($incident);

        $redirectUrl = $this->generateUrl(
            'caldera_cycleways_incident_show',
            [
                'slug' => $incident->getSlug()
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $post->setUser($this->getUser());
            $em->persist($post);

            $em->flush();

            /* Using the user’s referer will not work as the user might come from the writefailed page and would be
               redirected there again. */
            return new RedirectResponse($redirectUrl);
        }

        return $this->render(
            'CalderaCyclewaysBundle:Post:write.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
