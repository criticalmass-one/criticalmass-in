<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\BlogPost;
use App\Entity\Post;

class BlogPostCommentItem extends AbstractItem
{
    /* @var BlogPost $blogPost */
    protected $blogPost;

    /** @var string $blogPostTitle */
    protected $blogPostTitle;

    /** @var string $text */
    protected $text;

    /** @var Post $post */
    protected $post;

    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(BlogPost $blogPost): BlogPostCommentItem
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function setPost(Post $post): BlogPostCommentItem
    {
        $this->post = $post;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getBlogPostTitle(): string
    {
        return $this->blogPostTitle;
    }

    public function setBlogPostTitle(string $blogPostTitle): BlogPostCommentItem
    {
        $this->blogPostTitle = $blogPostTitle;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): BlogPostCommentItem
    {
        $this->text = $text;

        return $this;
    }
}
