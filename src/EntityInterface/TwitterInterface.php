<?php

namespace App\EntityInterface;

interface TwitterInterface
{
    public function setTwitter(string $twitter = null): TwitterInterface;

    public function getTwitter(): ?string;
}
