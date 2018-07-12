<?php

namespace App\EntityInterface;

interface PhotoInterface
{
    public function getImageName(): ?string;

    public function setImageName(string $imageName = null): PhotoInterface;
}
