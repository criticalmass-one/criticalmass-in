<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Post;
use App\Entity\Ride;

class PhotoCommentItem extends AbstractItem
{
    public array $postList = [];

    public ?Ride $ride = null;

    public function addPost(Post $post): PhotoCommentItem
    {
        $this->postList[] = $post;

        return $this;
    }

    public function getPostList(): array
    {
        return $this->postList;
    }

    public function setRide(Ride $ride): PhotoCommentItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }
}
