<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Ride;

class PhotoCommentItem extends AbstractItem
{
    /** @var array $postList*/
    public $postList = [];

    /** @var Ride $ride */
    public $ride;

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
