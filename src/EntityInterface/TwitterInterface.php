<?php declare(strict_types=1);

namespace App\EntityInterface;

interface TwitterInterface
{
    public function setTwitter(string $twitter = null): TwitterInterface;

    public function getTwitter(): ?string;
}
