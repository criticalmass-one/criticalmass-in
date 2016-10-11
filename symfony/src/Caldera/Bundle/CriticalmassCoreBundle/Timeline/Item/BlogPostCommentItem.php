<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;

class BlogPostCommentItem extends AbstractItem
{
    /**
     * @var string $username
     */
    public $username;

    /**
     * @var BlogPost $blogPost
     */
    public $blogPost;

    /**
     * @var string $blogPostTitle
     */
    public $blogPostTitle;

    /**
     * @var string $text
     */
    public $text;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return BlogPost
     */
    public function getBlogPost()
    {
        return $this->blogPost;
    }

    /**
     * @param BlogPost $blogPost
     */
    public function setBlogPost($blogPost)
    {
        $this->blogPost = $blogPost;
    }

    /**
     * @return string
     */
    public function getBlogPostTitle()
    {
        return $this->blogPostTitle;
    }

    /**
     * @param string $blogPostTitle
     */
    public function setBlogPostTitle($blogPostTitle)
    {
        $this->blogPostTitle = $blogPostTitle;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

}