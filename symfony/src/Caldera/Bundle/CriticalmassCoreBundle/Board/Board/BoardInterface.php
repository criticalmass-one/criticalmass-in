<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Board;

interface BoardInterface
{
    public function getTitle();
    public function getDescription();
    public function getThreadNumber();
    public function getPostNumber();
    public function getLastPost();
}