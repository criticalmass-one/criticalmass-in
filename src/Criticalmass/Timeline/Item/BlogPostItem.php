<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\BlogPost;

class BlogPostItem extends AbstractItem
{
    /** @var BlogPost $blogPost */
    public $blogPost;

    /** @var string $title */
    public $title;

    /** @var string $intro */
    public $intro;

    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(BlogPost $blogPost): BlogPostItem
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): BlogPostItem
    {
        $this->title = $title;

        return $this;
    }

    public function getIntro(): string
    {
        return $this->intro;
    }

    public function setIntro(string $intro): BlogPostItem
    {
        $this->intro = $intro;

        return $this;
    }

}
