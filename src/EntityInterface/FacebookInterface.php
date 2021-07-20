<?php declare(strict_types=1);

namespace App\EntityInterface;

interface FacebookInterface
{
    public function setFacebook(string $facebook = null): FacebookInterface;

    public function getFacebook(): ?string;
}
