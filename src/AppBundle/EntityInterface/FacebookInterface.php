<?php

namespace AppBundle\EntityInterface;

interface FacebookInterface
{
    public function setFacebook(string $facebook = null): FacebookInterface;

    public function getFacebook(): ?string;
}
