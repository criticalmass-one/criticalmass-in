<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class BlogPostParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $blogPost = null;

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
}
