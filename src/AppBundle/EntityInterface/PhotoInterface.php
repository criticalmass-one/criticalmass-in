<?php

namespace AppBundle\EntityInterface;

interface PhotoInterface
{
    public function getImageName(): ?string;

    public function setImageName(string $imageName = null): PhotoInterface;
}
