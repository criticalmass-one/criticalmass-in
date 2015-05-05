<?php

namespace Caldera\CriticalmassTwitterBundle\Utility\TweetBuilder;


use Caldera\CriticalmassTwitterBundle\Entity\Tweet;

class TweetBuilder {
    protected $jsonResponse;
    protected $tweets;

    public function setJsonResponse($jsonResponse)
    {
        $this->jsonResponse = $jsonResponse;
        $this->tweets = array();
    }

    public function getTweets()
    {
        return $this->tweets;
    }

    public function parse()
    {
        $parsedJson = json_decode($this->jsonResponse);

        foreach ($parsedJson->statuses as $status)
        {
            $tweet = new Tweet();

            $tweet->setText($status->text);
            $tweet->setDateTime(new \DateTime($status->created_at));
            $tweet->setUsername($status->user->name);
            $tweet->setScreenname($status->user->screen_name);
            $tweet->setProfileImageUrl($status->user->profile_image_url);
            $tweet->setTwitterId($status->id);

            $this->tweets[$tweet->getTwitterId()] = $tweet;
        }
    }
}