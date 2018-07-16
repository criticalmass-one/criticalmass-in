<?php

namespace App\Manager\PostManager;

use App\Manager\AbstractManager;
use App\Repository\PostRepository;

/**
 * @deprecated
 */
class PostManager extends AbstractManager
{
    /** @var PostRepository $postRepository */
    protected $postRepository = null;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->postRepository = $this->doctrine->getRepository('App:Post');
    }
}
