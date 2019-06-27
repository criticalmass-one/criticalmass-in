<?php declare(strict_types=1);

namespace App\Controller\Blog;

use App\Controller\AbstractController;
use App\Entity\BlogPost;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("photos")
 */
class BlogController extends AbstractController
{
    public function overviewAction(RegistryInterface $registry): Response
    {
        return $this->render('Blog/overview.html.twig', [
            'blog_posts' => $registry->getRepository(BlogPost::class)->findAll(),
        ]);
    }

    /**
     * @ParamConverter("blogPost", class="App:BlogPost", isOptional="false")
     */
    public function showAction(BlogPost $blogPost): Response
    {
        if (!$blogPost->isEnabled()) {
            throw $this->createNotFoundException();
        }

        return $this->render('Blog/blog_post.html.twig', [
            'blog_post' => $blogPost,
        ]);
    }
}
