<?php declare(strict_types=1);

namespace App\Controller\Blog;

use App\Controller\AbstractController;
use App\Entity\Blog;
use App\Entity\BlogPost;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    public function overviewAction(RegistryInterface $registry): Response
    {
        return $this->render('Blog/overview.html.twig', [
            'blog_posts' => $registry->getRepository(BlogPost::class)->findAll(),
        ]);
    }
}
