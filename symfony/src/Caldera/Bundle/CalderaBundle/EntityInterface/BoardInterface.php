<?php

namespace Caldera\Bundle\CalderaBundle\EntityInterface;


use Caldera\Bundle\CalderaBundle\Entity\Thread;

interface BoardInterface
{
    public function getTitle();
    public function setTitle($title);

    public function getThreadNumber();
    public function setThreadNumber($threadNumber);
    public function incThreadNumber();

    public function getPostNumber();
    public function setPostNumber($postNumber);
    public function incPostNumber();

    public function getLastThread();
    public function setLastThread(Thread $thread);
}