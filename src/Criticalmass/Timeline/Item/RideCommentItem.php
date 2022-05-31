<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Post;
use App\Entity\Ride;

class RideCommentItem extends AbstractItem
{
    protected ?Ride $ride = null;

    protected ?string $rideTitle = null;

    protected ?string $text = null;

    protected ?Post $post = null;

    protected ?bool $rideEnabled = null;

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideCommentItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function setPost(Post $post): RideCommentItem
    {
        $this->post = $post;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): RideCommentItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): RideCommentItem
    {
        $this->text = $text;

        return $this;
    }

    public function setRideEnabled(bool $rideEnabled): RideCommentItem
    {
        $this->rideEnabled = $rideEnabled;

        return $this;
    }

    public function isRideEnabled(): bool
    {
        return $this->rideEnabled;
    }
}
