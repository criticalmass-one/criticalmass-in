<?php declare(strict_types=1);

namespace App\Factory\BlogPost;

use App\Entity\BlogPost;
use Symfony\Component\Security\Core\User\UserInterface;

class BlogPostFactory implements BlogPostFactoryInterface
{
    /** @var BlogPost $blogPost */
    protected $blogPost;

    public function __construct()
    {
        $this->blogPost = new BlogPost();

        $this->blogPost
            ->setCreatedAt(new \DateTime())
            ->setEnabled(true);
    }

    public function withUser(UserInterface $user): BlogPostFactoryInterface
    {
        $this->blogPost->setUser($user);

        return $this;
    }

    public function build(): BlogPost
    {
        return $this->blogPost;
    }
}