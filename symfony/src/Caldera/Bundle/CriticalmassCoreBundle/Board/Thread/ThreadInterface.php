<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Thread;

interface ThreadInterface
{
    public function getTitle();
    public function getPostNumber();
    public function getViewNumber();
    public function getUser();
    public function getLastPost();
}