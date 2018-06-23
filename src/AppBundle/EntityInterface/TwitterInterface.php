<?php

namespace Criticalmass\Bundle\AppBundle\EntityInterface;

interface TwitterInterface
{
    public function setTwitter(string $twitter = null): TwitterInterface;

    public function getTwitter(): ?string;
}
