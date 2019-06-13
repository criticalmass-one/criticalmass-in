<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class BlogPostParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $blogPost = $this->findBlogPostById($request);

        $blogPostSlug = $request->get('slug');

        if ($blogPostSlug) {
            $blogPost = $this->registry->getRepository(BlogPost::class)->findOneBySlug($blogPostSlug);
        }

        if ($blogPost) {
            $request->attributes->set($configuration->getName(), $blogPost);
        } else {
            $this->notFound($configuration);
        }
    }

    protected function findBlogPostById(Request $request): ?BlogPost
    {
        $blogPostId = $request->get('blogPostId');

        if ($blogPostId) {
            $blogPost = $this->registry->getRepository(BlogPost::class)->find($blogPostId);

            return $blogPost;
        }

        return null;
    }
}
