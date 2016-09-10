<?php

namespace Caldera\Bundle\CriticalmassBlogBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;
use Caldera\Bundle\CalderaBundle\Entity\Post;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PostType;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AbstractController
{
    public function writeAction(
        Request $request, 
        BlogPost $blogPost
    ): Response
    {
        $post = new Post();

        $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_blog_comment_write', array('slug' => $blogPost->getSlug()))));
        $post->setBlogPost($blogPost);

        $redirectUrl = $this->generateUrl('caldera_criticalmass_blog_post', array('slug' => $blogPost->getSlug()));

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

        return $this->render('CalderaCriticalmassBlogBundle:Comment:write.html.twig', array('form' => $form->createView()));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(
        Request $request, 
        BlogPost $blogPost
    ): Response
    {
        $criteria =
        [
            'enabled' => true,
            'blogPost' => $blogPost
        ];

        $posts = $this->getPostRepository()->findBy($criteria, array('dateTime' => 'DESC'));

        return $this->render('CalderaCriticalmassBlogBundle:Comment:list.html.twig', array('posts' => $posts));
    }
}
