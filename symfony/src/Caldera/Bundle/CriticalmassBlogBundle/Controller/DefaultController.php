<?php

namespace Caldera\Bundle\CriticalmassBlogBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\Blog;
use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends AbstractController
{
    use ViewStorageTrait;

    protected function getBlog(Request $request): Blog
    {
        $hostname = $request->getHost();

        $blog = $this->getBlogRepository()->findOneByHostname($hostname);

        if (!$blog) {
            throw $this->createNotFoundException();
        }

        return $blog;
    }

    public function indexAction(Request $request)
    {
        $blog = $this->getBlog($request);
        $posts = $this->getBlogPostRepository()->findByBlog($blog, ['dateTime' => 'DESC']);

        return $this->render(
            'CalderaCriticalmassBlogBundle:Default:index.html.twig',
            [
                'posts' => $posts,
                'blog' => $blog
            ]
        );
    }

    public function viewAction(Request $request, $slug)
    {
        $blog = $this->getBlog($request);
        $post = $this->getBlogPostRepository()->findOneBySlug($slug);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $this->countBlogPostView($post);

        return $this->render(
            'CalderaCriticalmassBlogBundle:Default:view.html.twig',
            [
                'post' => $post,
                'blog' => $blog
            ]
        );
    }
}
