<?php declare(strict_types=1);

namespace App\Controller\Blog;

use App\Controller\AbstractController;
use App\Entity\BlogPost;
use App\Event\View\ViewEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("blog")
 */
class BlogController extends AbstractController
{
    public function overviewAction(RegistryInterface $registry): Response
    {
        return $this->render('Blog/overview.html.twig', [
            'blog_posts' => $registry->getRepository(BlogPost::class)->findForBlogFrontpage(),
        ]);
    }

    /**
     * @ParamConverter("blogPost", class="App:BlogPost", isOptional="false")
     */
    public function showAction(BlogPost $blogPost, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$blogPost->isEnabled()) {
            throw $this->createNotFoundException();
        }

        $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($blogPost));

        return $this->render('Blog/blog_post.html.twig', [
            'blog_post' => $blogPost,
        ]);
    }
}
