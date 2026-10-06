<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Post;

class StatusPostItem extends AbstractItem
{
    protected ?Post $post = null;

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): StatusPostItem
    {
        $this->post = $post;

        return $this;
    }
}
