<?php

namespace AppBundle\EntityInterface;

interface UrlInterface
{
    public function setUrl(string $url = null): UrlInterface;

    public function getUrl(): ?string;
}
